<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DeskAdminSubmissionToolsTest extends TestCase
{
    use RefreshDatabase;

    private function disableAbac(): void
    {
        Cache::put('settings.abac_enabled', false);
        Cache::put('settings.abac_location_enforcement', false);
        Cache::put('settings.abac_hq_bypass', true);
    }

    private function deskAdmin(string $scope = 'LA'): User
    {
        return User::factory()->create([
            'role' => 'state',
            'user_category' => 'desk_admin',
            'primary_location_type' => 'state',
            'primary_location_code' => $scope,
            'access_level' => 1,
        ]);
    }

    private function officer(): User
    {
        return User::factory()->create([
            'role' => 'officer',
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'primary_location_code' => 'LA',
            'access_level' => 0,
        ]);
    }

    private function submission(User $officer, array $overrides = []): Application
    {
        $createdAt = $overrides['created_at'] ?? null;
        unset($overrides['created_at']);

        $application = Application::create(array_merge([
            'user_id' => $officer->id,
            'status' => 'pending',
            'workflow_stage' => 'desk_review',
            'category' => 'state',
            'scope_code' => 'LA',
            'return_data' => [
                'report_period' => '2026-08',
                'reporting_officer' => $officer->name,
                'staff_strength' => ['dcg' => ['male' => '1', 'female' => '2']],
            ],
        ], $overrides));

        if ($createdAt !== null) {
            $application->timestamps = false;
            $application->created_at = $createdAt;
            $application->save();
        }

        return $application;
    }

    public function test_dashboard_lists_approved_submissions_with_view_and_download_links(): void
    {
        $this->disableAbac();

        $admin = $this->deskAdmin();
        $approved = $this->submission($this->officer(), ['status' => 'approved', 'workflow_stage' => 'approved']);

        $response = $this->actingAs($admin)->get(route('user.desk.home'));

        $response->assertOk();
        $response->assertSee('Approved Submissions');
        $response->assertSee('#' . $approved->id);
        $response->assertSee(route('desk.admin.submissions.download', $approved), false);
    }

    public function test_preview_renders_sectional_content_and_document_cards(): void
    {
        $this->disableAbac();
        Storage::fake(config('filesystems.default'));
        Storage::put('supporting-documents/hrm/note.pdf', 'pdf-bytes');

        $admin = $this->deskAdmin();
        $application = $this->submission($this->officer(), [
            'return_data' => [
                'report_period' => '2026-08',
                'reporting_officer' => 'Officer One',
                'staff_strength' => ['dcg' => ['male' => '1', 'female' => '2']],
                'supporting_documents' => ['supporting-documents/hrm/note.pdf'],
            ],
        ]);

        $response = $this->actingAs($admin)->get(route('desk.admin.submissions.show', $application));

        $response->assertOk();
        $response->assertSee('Staff Strength');
        $response->assertSee('Dcg');
        $response->assertSee('Uploaded Documents');
        $response->assertSee('note.pdf');
        $response->assertSee(route('desk.admin.submissions.document', [$application, 'supporting', 0]), false);
    }

    public function test_document_is_streamed_inline_and_downloadable(): void
    {
        $this->disableAbac();
        Storage::fake(config('filesystems.default'));
        Storage::put('supporting-documents/hrm/photo.png', 'png-bytes');

        $admin = $this->deskAdmin();
        $application = $this->submission($this->officer(), [
            'return_data' => ['supporting_documents' => ['supporting-documents/hrm/photo.png']],
        ]);

        $viewResponse = $this->actingAs($admin)
            ->get(route('desk.admin.submissions.document', [$application, 'supporting', 0]));
        $viewResponse->assertOk();
        $this->assertStringContainsString('inline', $viewResponse->headers->get('content-disposition'));

        $downloadResponse = $this->actingAs($admin)
            ->get(route('desk.admin.submissions.document', [$application, 'supporting', 0]) . '?download=1');
        $downloadResponse->assertOk();
        $this->assertStringContainsString('attachment', $downloadResponse->headers->get('content-disposition'));
    }

    public function test_document_route_rejects_out_of_scope_user_and_bad_index(): void
    {
        $this->disableAbac();
        Storage::fake(config('filesystems.default'));
        Storage::put('supporting-documents/hrm/photo.png', 'png-bytes');

        $application = $this->submission($this->officer(), [
            'return_data' => ['supporting_documents' => ['supporting-documents/hrm/photo.png']],
        ]);

        $outOfScope = $this->deskAdmin('KD');
        $this->actingAs($outOfScope)
            ->get(route('desk.admin.submissions.document', [$application, 'supporting', 0]))
            ->assertForbidden();

        $this->actingAs($this->deskAdmin())
            ->get(route('desk.admin.submissions.document', [$application, 'supporting', 5]))
            ->assertNotFound();
    }

    public function test_single_submission_csv_download(): void
    {
        $this->disableAbac();

        $admin = $this->deskAdmin();
        $application = $this->submission($this->officer());

        $response = $this->actingAs($admin)->get(route('desk.admin.submissions.download', $application));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
        $this->assertStringContainsString('staff_strength', $response->streamedContent());
        $this->assertStringContainsString('dcg.male', $response->streamedContent());
    }

    public function test_reports_page_filters_by_date_and_status(): void
    {
        $this->disableAbac();

        $admin = $this->deskAdmin();
        $officer = $this->officer();

        $inRange = $this->submission($officer, ['created_at' => '2026-08-10 10:00:00', 'status' => 'approved', 'workflow_stage' => 'approved']);
        $outOfRange = $this->submission($officer, ['created_at' => '2026-06-01 10:00:00']);

        $response = $this->actingAs($admin)->get(route('desk.admin.reports', [
            'date_from' => '2026-08-01',
            'date_to' => '2026-08-31',
        ]));

        $response->assertOk();
        $response->assertSee('#' . $inRange->id);
        $response->assertDontSee('#' . $outOfRange->id);
    }

    public function test_reports_csv_export(): void
    {
        $this->disableAbac();

        $admin = $this->deskAdmin();
        $application = $this->submission($this->officer(), ['created_at' => '2026-08-10 10:00:00']);

        $response = $this->actingAs($admin)->get(route('desk.admin.reports', [
            'date_from' => '2026-08-01',
            'date_to' => '2026-08-31',
            'format' => 'csv',
        ]));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
        $this->assertStringContainsString('Report Period', $response->streamedContent());
        $this->assertStringContainsString((string) $application->id, $response->streamedContent());
    }

    public function test_reports_page_rejects_non_approvers(): void
    {
        $this->disableAbac();

        $this->actingAs($this->officer())->get(route('desk.admin.reports'))->assertForbidden();
    }
}
