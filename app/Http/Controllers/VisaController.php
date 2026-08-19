<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;

class VisaController extends Controller
{
    // Dashboard
    public function dashboard()
    {
        if (auth()->user()->user_category === 'directorate_user') {
            return redirect()->route('visa.submissions');
        }

        $submissionsQuery = Application::where('user_id', auth()->id())->where('type', 'visa');
        $draftReportsCount = (clone $submissionsQuery)->where('status', 'draft')->count();
        $pendingReportsCount = (clone $submissionsQuery)->where('status', 'pending')->count();
        $approvedReportsCount = (clone $submissionsQuery)->where('status', 'approved')->count();

        $latest = Application::where('user_id', auth()->id())
            ->where('type', 'visa')
            ->latest()
            ->first();

        $staffStrengthCount = 0;
        $emigrantCount = 0;
        $quotaCount = 0;
        $residencePermitsCount = 0;
        $ftzEnterprisesCount = 0;
        $cerpacIssuedCount = 0;
        $visaApplicationsCount = 0;
        $trvCount = 0;
        $prvCount = 0;
        $etwpCount = 0;
        $visaSummaryCount = 0;
        $ecowasCount = 0;
        $africanCount = 0;

        if ($latest && is_array($latest->return_data)) {
            // 1. Staff Strength
            if (isset($latest->return_data['staff'])) {
                foreach ($latest->return_data['staff'] as $cadre => $genders) {
                    $staffStrengthCount += (int)($genders['male'] ?? 0) + (int)($genders['female'] ?? 0);
                }
            }

            // 2. e-Migrant Centre
            if (isset($latest->return_data['emigrant'])) {
                foreach ($latest->return_data['emigrant'] as $em) {
                    $emigrantCount += (int)($em['regular'] ?? 0) + (int)($em['irregular'] ?? 0);
                }
            }

            // 3. Quota Administration
            if (isset($latest->return_data['quota'])) {
                foreach ($latest->return_data['quota'] as $q) {
                    $quotaCount += (int)($q['positions'] ?? 0);
                }
            }

            // 4. Residence Permits
            if (isset($latest->return_data['residence_temporary'])) {
                foreach ($latest->return_data['residence_temporary'] as $item) {
                    foreach ($item as $val) {
                        $residencePermitsCount += (int) $val;
                    }
                }
            }
            if (isset($latest->return_data['residence_permanent'])) {
                foreach ($latest->return_data['residence_permanent'] as $item) {
                    foreach ($item as $val) {
                        $residencePermitsCount += (int) $val;
                    }
                }
            }

            // 5. Free Trade Zone
            if (isset($latest->return_data['ftz']['enterprises'])) {
                $ftzEnterprisesCount = (int) $latest->return_data['ftz']['enterprises'];
            }

            // 6. CERPAC Production
            if (isset($latest->return_data['cerpac'])) {
                if (is_array($latest->return_data['cerpac'])) {
                    if (isset($latest->return_data['cerpac']['issued'])) {
                        $cerpacIssuedCount = (int) $latest->return_data['cerpac']['issued'];
                    } else {
                        foreach ($latest->return_data['cerpac'] as $item) {
                            $cerpacIssuedCount += (int) ($item['issued'] ?? 0);
                        }
                    }
                } else {
                    $cerpacIssuedCount = (int) $latest->return_data['cerpac'];
                }
            }

            // 7. Visa Applications
            if (isset($latest->return_data['visa_applications'])) {
                foreach ($latest->return_data['visa_applications'] as $cls => $fields) {
                    $visaApplicationsCount += (int) ($fields['applications'] ?? 0);
                }
            }

            // 8. TRV
            if (isset($latest->return_data['trv'])) {
                foreach ($latest->return_data['trv'] as $cls => $fields) {
                    $trvCount += (int)($fields['applications'] ?? 0);
                }
            }

            // 9. PRV
            if (isset($latest->return_data['prv'])) {
                foreach ($latest->return_data['prv'] as $cls => $fields) {
                    $prvCount += (int)($fields['applications'] ?? 0);
                }
            }

            // 10. e-TWP
            if (isset($latest->return_data['etwp'])) {
                foreach ($latest->return_data['etwp'] as $cls => $fields) {
                    $etwpCount += (int)($fields['applications'] ?? 0);
                }
            }

            // 11. Visa Summary
            $visaSummaryCount = $visaApplicationsCount + $trvCount + $prvCount + $etwpCount;

            // 12. ECOWAS Affairs
            if (isset($latest->return_data['ecowas'])) {
                foreach ($latest->return_data['ecowas'] as $row) {
                    $ecowasCount += (int)($row['male'] ?? 0) + (int)($row['female'] ?? 0);
                }
            }

            // 13. African Affairs
            if (isset($latest->return_data['african'])) {
                foreach ($latest->return_data['african'] as $row) {
                    $africanCount += (int)($row['male'] ?? 0) + (int)($row['female'] ?? 0);
                }
            }
        }

        $recentSubmissions = Application::where('user_id', auth()->id())
            ->where('type', 'visa')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        return view('user.visa.dashboard', compact(
            'staffStrengthCount',
            'emigrantCount',
            'quotaCount',
            'residencePermitsCount',
            'ftzEnterprisesCount',
            'cerpacIssuedCount',
            'visaApplicationsCount',
            'trvCount',
            'prvCount',
            'etwpCount',
            'visaSummaryCount',
            'ecowasCount',
            'africanCount',
            'recentSubmissions',
            'draftReportsCount',
            'pendingReportsCount',
            'approvedReportsCount'
        ));
    }

    // Annual Report Workspace
    public function create(Request $request)
    {
        $period = $request->query('year', date('Y'));
        $id = $request->query('id');

        if ($id) {
            $application = Application::where('type', 'visa')
                ->where('id', $id)
                ->first();
            
            if ($application) {
                $period = $application->period;
                $user = auth()->user();
                if ($user->user_category === 'directorate_user' && $application->user_id !== $user->id) {
                    abort(403, 'Unauthorized access.');
                }
            }
        } else {
            $application = Application::where('user_id', auth()->id())
                ->where('type', 'visa')
                ->where('period', $period)
                ->first();
        }

        return view('user.visa.report', compact('application', 'period'));
    }

    // Store Report
    public function store(Request $request)
    {
        $validated = $request->validate([
            'report_year' => 'required|integer',
            'remarks' => 'nullable|string',
        ]);

        $status = $request->input('report_status', ($request->input('action') === 'draft' ? 'draft' : 'pending'));
        $period = $request->input('report_year');
        $comments = $request->input('remarks');

        // Exclude framework and action tokens
        $returnData = $request->except(['_token', 'action', 'report_year', 'remarks']);

        $application = Application::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'type' => 'visa',
                'period' => $period,
            ],
            [
                'status' => $status,
                'return_data' => $returnData,
                'comments' => $comments,
            ]
        );

        if ($status === 'pending') {
            $admins = \App\Models\User::where(function ($query) {
                $query->where('user_category', 'admin')
                      ->orWhere(function ($q) {
                          $q->where('user_category', 'directorate_admin')
                            ->where('primary_location_code', 'VISA');
                      });
            })->get();
            foreach ($admins as $adm) {
                \App\Models\UserNotification::create([
                    'user_id' => $adm->id,
                    'type' => 'pending',
                    'title' => 'New Report Awaiting Approval (Visa & Residence - ' . $period . ')',
                    'description' => 'A report has been forwarded for approval by ' . auth()->user()->name . '.',
                    'tag' => 'info',
                    'action_url' => route('visa.submissions'),
                    'is_read' => false,
                ]);
            }
        }

        $message = $status === 'draft' 
            ? 'Visa & Residence Annual Report draft saved successfully.'
            : 'Your report has been sent for approval and you will be notified when approval is given.';

        if ($status === 'draft') {
            return redirect()
                ->route('visa.report', ['year' => $period])
                ->with('status', $message);
        }

        // Find next available year that is not pending, approved, or submitted
        $nextYear = null;
        for ($y = date('Y'); $y >= 2023; $y--) {
            $exists = Application::where('user_id', auth()->id())
                ->where('type', 'visa')
                ->where('period', $y)
                ->whereIn('status', ['pending', 'approved', 'submitted'])
                ->exists();
            if (!$exists) {
                $nextYear = $y;
                break;
            }
        }

        $params = $nextYear ? ['year' => $nextYear] : [];

        return redirect()
            ->route('visa.report', $params)
            ->with('status', $message);
    }

    // Reports
    public function reports()
    {
        if (auth()->user()->user_category === 'directorate_user') {
            return redirect()->route('visa.report');
        }

        $submissions = Application::where('user_id', auth()->id())
            ->where('type', 'visa')
            ->orderBy('period', 'desc')
            ->get();

        // Calculate cumulative statistics across all returns in database
        $totalSubmissions = $submissions->count();
        $cumulativeResidencePermits = 0;
        $cumulativeVisaApplications = 0;
        $cumulativeCerpacIssued = 0;
        $cumulativeMigrants = 0;

        $yearlyData = [];

        foreach ($submissions as $sub) {
            $residence = 0;
            $visas = 0;
            $cerpac = 0;
            $migrants = 0;

            if (is_array($sub->return_data)) {
                // Residence Permits
                if (isset($sub->return_data['residence_temporary'])) {
                    foreach ($sub->return_data['residence_temporary'] as $item) {
                        foreach ($item as $val) {
                            $residence += (int) $val;
                        }
                    }
                }
                if (isset($sub->return_data['residence_permanent'])) {
                    foreach ($sub->return_data['residence_permanent'] as $item) {
                        foreach ($item as $val) {
                            $residence += (int) $val;
                        }
                    }
                }

                // Visa Applications
                if (isset($sub->return_data['visa_applications'])) {
                    foreach ($sub->return_data['visa_applications'] as $cls => $fields) {
                        $visas += (int) ($fields['applications'] ?? 0);
                    }
                }

                // CERPAC
                if (isset($sub->return_data['cerpac'])) {
                    if (is_array($sub->return_data['cerpac'])) {
                        if (isset($sub->return_data['cerpac']['issued'])) {
                            $cerpac = (int) $sub->return_data['cerpac']['issued'];
                        } else {
                            foreach ($sub->return_data['cerpac'] as $item) {
                                $cerpac += (int) ($item['issued'] ?? 0);
                            }
                        }
                    } else {
                        $cerpac = (int) $sub->return_data['cerpac'];
                    }
                }

                // e-Migrant
                if (isset($sub->return_data['emigrant'])) {
                    foreach ($sub->return_data['emigrant'] as $em) {
                        $migrants += (int)($em['regular'] ?? 0) + (int)($em['irregular'] ?? 0);
                    }
                }
            }

            $cumulativeResidencePermits += $residence;
            $cumulativeVisaApplications += $visas;
            $cumulativeCerpacIssued += $cerpac;
            $cumulativeMigrants += $migrants;

            $yearlyData[] = [
                'year' => $sub->period,
                'status' => $sub->status,
                'residence' => $residence,
                'visas' => $visas,
                'cerpac' => $cerpac,
                'migrants' => $migrants,
                'updated_at' => $sub->updated_at->format('d M Y'),
            ];
        }

        return view('user.visa.reports', compact(
            'totalSubmissions',
            'cumulativeResidencePermits',
            'cumulativeVisaApplications',
            'cumulativeCerpacIssued',
            'cumulativeMigrants',
            'yearlyData'
        ));
    }

    // Submitted Reports
    public function submissions()
    {

        $user = auth()->user();
        $query = Application::where('type', 'visa');

        if ($user->user_category === 'directorate_admin' || $user->role === 'admin') {
            // Supervisors see all visa submissions
        } else {
            $query->where('user_id', $user->id);
        }

        $submissions = $query->orderBy('created_at', 'desc')->get();

        return view('user.visa.submissions', compact('submissions'));
    }

    // Approve Return
    public function approve(Request $request, $id)
    {
        if (auth()->user()->user_category === 'directorate_user') {
            abort(403, 'Unauthorized action.');
        }

        $application = Application::where('type', 'visa')->findOrFail($id);
        
        $application->update([
            'status' => 'approved',
            'comments' => $request->input('remarks') ?? 'Approved by supervisor.',
        ]);

        \App\Models\UserNotification::create([
            'user_id' => $application->user_id,
            'type' => 'approval',
            'title' => 'Report Approved (Visa & Residence - ' . $application->period . ')',
            'description' => 'Your report has been approved by the Admin. You can now submit it.',
            'tag' => 'success',
            'action_url' => route('visa.report', ['year' => $application->period]),
            'is_read' => false,
        ]);

        return redirect()
            ->route('visa.submissions')
            ->with('status', 'Visa & Residence Annual Report approved successfully.');
    }

    // Query Return
    public function query(Request $request, $id)
    {
        if (auth()->user()->user_category === 'directorate_user') {
            abort(403, 'Unauthorized action.');
        }

        $application = Application::where('type', 'visa')->findOrFail($id);
        
        $application->update([
            'status' => 'queried',
            'comments' => $request->input('remarks') ?? 'Queried by supervisor.',
        ]);

        \App\Models\UserNotification::create([
            'user_id' => $application->user_id,
            'type' => 'query',
            'title' => 'Report Queried (Visa & Residence - ' . $application->period . ')',
            'description' => 'Your report has been queried: ' . ($request->input('remarks') ?? 'Please review details.'),
            'tag' => 'warning',
            'action_url' => route('visa.report', ['year' => $application->period]),
            'is_read' => false,
        ]);

        return redirect()
            ->route('visa.submissions')
            ->with('status', 'Visa & Residence Annual Report queried successfully.');
    }

    // Submit Approved Return
    public function submit($id)
    {
        // Officers can submit their own approved returns
        $query = Application::where('type', 'visa')->where('status', 'approved');
        if (auth()->user()->user_category === 'directorate_user') {
            $query->where('user_id', auth()->id());
        }
        $application = $query->findOrFail($id);

        $application->update([
            'status' => 'submitted',
        ]);

        // Find next available year that is not pending, approved, or submitted
        $nextYear = null;
        for ($y = date('Y'); $y >= 2023; $y--) {
            $exists = Application::where('user_id', auth()->id())
                ->where('type', 'visa')
                ->where('period', $y)
                ->whereIn('status', ['pending', 'approved', 'submitted'])
                ->exists();
            if (!$exists) {
                $nextYear = $y;
                break;
            }
        }

        $params = $nextYear ? ['year' => $nextYear] : [];

        return redirect()
            ->route('visa.report', $params)
            ->with('status', 'Visa & Residence Annual Report submitted successfully to Headquarters.');
    }
}