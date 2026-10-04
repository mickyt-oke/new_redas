<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Concerns\ResolvesHashedModels;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationComment;
use App\Models\User;
use App\Services\NisFormationService;
use App\Services\SubmissionWorkflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ZonalUserController extends Controller
{
    use ResolvesHashedModels;

    public function dashboard(): View
    {
        $user = Auth::user();

        $baseQuery = Schema::hasTable('applications')
            ? Application::query()->where('user_id', $user?->id)
            : null;

        $submissions = $baseQuery !== null
            ? (clone $baseQuery)->orderByDesc('created_at')->limit(5)->get()
            : collect();

        $totalSubmissions = $baseQuery !== null ? (clone $baseQuery)->count() : 0;
        $pendingSubmissions = $baseQuery !== null
            ? (clone $baseQuery)->whereIn('status', ['pending', 'submitted'])->count()
            : 0;
        $approvedSubmissions = $baseQuery !== null
            ? (clone $baseQuery)->where('status', 'approved')->count()
            : 0;
        $queriedSubmissions = $baseQuery !== null
            ? (clone $baseQuery)->whereIn('status', ['queried', 'rejected', 'returned'])->count()
            : 0;

        return view('user.zones.dashboard', [
            'submissions' => $submissions,
            'totalSubmissions' => $totalSubmissions,
            'pendingSubmissions' => $pendingSubmissions,
            'approvedSubmissions' => $approvedSubmissions,
            'queriedSubmissions' => $queriedSubmissions,
            'commandName' => $this->commandNameForUser($user),
        ]);
    }

    public function createReturn(): View
    {
        return view('user.zones.create-return', [
            'commandName' => $this->commandNameForUser(Auth::user()),
        ]);
    }

    public function storeReturn(Request $request): RedirectResponse
    {
        $validated = $this->returnValidationRules($request);

        $user = Auth::user();
        $returnData = $request->except(['_token', 'data_consent', 'attachments']);
        $returnData['report_period'] = $request->input('period');
        $returnData['command_name'] = $this->commandNameForUser($user);
        $returnData['reporting_officer'] = $user?->name ?? $request->input('reporting_officer');
        $returnData['attachments'] = $this->storeUploadedFiles($request, 'attachments');

        try {
            SubmissionWorkflow::create($user, $returnData);
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'The return could not be submitted because of a system error. Please try again.');
        }

        return redirect()->route('user.zones.returns.index')
            ->with('status', 'Zonal return submitted successfully and routed to the zonal commander for review.');
    }

    public function submissions(): View
    {
        $baseQuery = Application::query()->where('user_id', Auth::id());

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'returned' => (clone $baseQuery)->where('status', 'returned')->count(),
        ];

        $submissions = (clone $baseQuery)->latest()->paginate(15);

        return view('user.zones.submissions', [
            'submissions' => $submissions,
            'stats' => $stats,
        ]);
    }

    public function showSubmission(string $applicationHash): View
    {
        $application = $this->resolveApplication($applicationHash);

        abort_unless($application->user_id === Auth::id(), 403);

        $application->load('reviewComments.user');

        return view('user.zones.show', [
            'application' => $application,
            'commandName' => $this->commandNameForUser(Auth::user()),
        ]);
    }

    public function editSubmission(string $applicationHash): View|RedirectResponse
    {
        $application = $this->resolveApplication($applicationHash);

        abort_unless($application->user_id === Auth::id(), 403);

        if (strtolower((string) $application->status) === 'approved') {
            return redirect()->route('user.zones.returns.index')
                ->with('status', 'Approved returns cannot be edited.');
        }

        return view('user.zones.create-return', [
            'editing' => $application,
            'commandName' => $this->commandNameForUser(Auth::user()),
        ]);
    }

    public function updateSubmission(Request $request, string $applicationHash): RedirectResponse
    {
        $application = $this->resolveApplication($applicationHash);

        abort_unless($application->user_id === Auth::id(), 403);
        abort_if(strtolower((string) $application->status) === 'approved', 403, 'Approved returns cannot be edited.');

        $validated = $this->returnValidationRules($request);

        $user = Auth::user();
        $existingAttachments = $application->return_data['attachments'] ?? [];
        if (! is_array($existingAttachments)) {
            $existingAttachments = [];
        }

        $returnData = $request->except(['_token', '_method', 'data_consent', 'attachments']);
        $returnData['report_period'] = $request->input('period');
        $returnData['command_name'] = $this->commandNameForUser($user);
        $returnData['reporting_officer'] = $user?->name ?? $request->input('reporting_officer');
        $returnData['attachments'] = $request->hasFile('attachments')
            ? $this->storeUploadedFiles($request, 'attachments')
            : $existingAttachments;

        $initialStage = SubmissionWorkflow::initialStageForUser($user);
        $path = $application->workflow_path ?? [];
        $path[] = [
            'stage' => SubmissionWorkflow::STAGE_SUBMITTED,
            'by' => $user->id,
            'at' => now()->toDateTimeString(),
            'action' => 'resubmitted',
        ];

        try {
            $application->update([
                'return_data' => $returnData,
                'status' => 'pending',
                'workflow_stage' => $initialStage,
                'workflow_path' => $path,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'The return could not be updated because of a system error. Please try again.');
        }

        SubmissionWorkflow::recordComment(
            $application,
            $user,
            SubmissionWorkflow::STAGE_SUBMITTED,
            ApplicationComment::ACTION_RESUBMITTED
        );

        SubmissionWorkflow::notifyPendingReviewers($application);

        return redirect()->route('user.zones.returns.index')
            ->with('status', 'Return updated and resubmitted successfully.');
    }

    public function submissionDocument(Request $request, string $applicationHash, string $collection, int $index)
    {
        $application = $this->resolveApplication($applicationHash);

        abort_unless($application->user_id === $request->user()->id, 403);
        abort_unless(in_array($collection, ['supporting', 'attachments'], true), 404);

        $key = $collection === 'supporting' ? 'supporting_documents' : 'attachments';
        $files = array_values(array_filter(
            (array) ($application->return_data[$key] ?? []),
            'is_string'
        ));

        $path = $files[$index] ?? null;
        abort_unless(is_string($path) && $path !== '', 404);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk();
        abort_unless($disk->exists($path), 404);

        if ($request->boolean('download')) {
            return $disk->download($path, basename($path));
        }

        return $disk->response($path, basename($path));
    }

    public function destroySubmission(string $applicationHash): RedirectResponse
    {
        $application = $this->resolveApplication($applicationHash);

        $user = Auth::user();

        abort_unless($user !== null && SubmissionWorkflow::deletableBy($application, $user), 403);

        $data = is_array($application->return_data) ? $application->return_data : [];

        foreach ((array) ($data['attachments'] ?? []) as $path) {
            if (! is_string($path) || $path === '') {
                continue;
            }
            try {
                if (Storage::disk()->exists($path)) {
                    Storage::disk()->delete($path);
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $application->delete();

        return redirect()->route('user.zones.returns.index')
            ->with('status', 'Return deleted successfully.');
    }

    public static function commandNameForUser(?User $user): ?string
    {
        $code = $user?->primary_location_code;

        if ($code === null || $code === '') {
            return null;
        }

        $label = NisFormationService::labelForCode($code);

        return $label ?? strtoupper((string) $code);
    }

    private function returnValidationRules(Request $request): array
    {
        return $request->validate([
            'command_name' => ['required', 'string', 'max:120'],
            'period' => ['required', 'date_format:Y-m'],
            'return_type' => ['required', 'in:monthly,quarterly,biannual,annual,special'],
            'reporting_officer' => ['required', 'string', 'max:120'],
            'data_consent' => ['required', 'accepted'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:20480'],
            'personnel' => ['nullable', 'array'],
            'operations' => ['nullable', 'array'],
            'logistics' => ['nullable', 'array'],
            'finance' => ['nullable', 'array'],
            'challenges' => ['nullable', 'array'],
        ]);
    }

    private function storeUploadedFiles(Request $request, string $key): array
    {
        $paths = [];

        foreach ((array) $request->file($key, []) as $file) {
            if ($file && $file->isValid()) {
                $paths[] = $file->store('supporting-documents/zonal');
            }
        }

        return $paths;
    }
}
