<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PdfReportTest extends TestCase
{
    use RefreshDatabase;

    private function disableAbac(): void
    {
        Cache::put('settings.abac_enabled', false);
        Cache::put('settings.abac_location_enforcement', false);
        Cache::put('settings.abac_hq_bypass', true);
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

    private function approvedSubmission(User $officer): Application
    {
        return Application::create([
            'user_id' => $officer->id,
            'status' => 'approved',
            'workflow_stage' => 'approved',
            'category' => 'state',
            'scope_code' => 'LA',
            'return_data' => [
                'report_period' => '2026-08',
                'reporting_officer' => $officer->name,
                'staff_strength' => ['dcg' => ['male' => '1', 'female' => '2']],
            ],
        ]);
    }

    public function test_owner_can_download_submission_pdf(): void
    {
        $officer = $this->officer();
        $application = $this->approvedSubmission($officer);

        $response = $this->actingAs($officer)->get(route('user.submissions.pdf', $application));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
    }

    public function test_in_scope_approver_can_download_submission_pdf(): void
    {
        $this->disableAbac();

        $admin = $this->deskAdmin('LA');
        $application = $this->approvedSubmission($this->officer());

        $this->actingAs($admin)->get(route('user.submissions.pdf', $application))->assertOk();
    }

    public function test_out_of_scope_or_unrelated_user_cannot_download_submission_pdf(): void
    {
        $this->disableAbac();

        $application = $this->approvedSubmission($this->officer());

        $this->actingAs($this->deskAdmin('KD'))
            ->get(route('user.submissions.pdf', $application))
            ->assertForbidden();

        $this->actingAs($this->officer())
            ->get(route('user.submissions.pdf', $application))
            ->assertForbidden();
    }

    public function test_reports_page_exports_pdf(): void
    {
        $this->disableAbac();

        $admin = $this->deskAdmin();
        $this->approvedSubmission($this->officer());

        $response = $this->actingAs($admin)->get(route('desk.admin.reports', [
            'date_from' => '2026-08-01',
            'date_to' => '2026-08-31',
            'format' => 'pdf',
        ]));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
    }

    public function test_desk_admin_download_supports_pdf_format(): void
    {
        $this->disableAbac();

        $admin = $this->deskAdmin();
        $application = $this->approvedSubmission($this->officer());

        $response = $this->actingAs($admin)->get(route('desk.admin.submissions.download', $application) . '?format=pdf');

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
    }

    public function test_archive_lists_completed_returns_with_pdf_download_for_owner(): void
    {
        $this->disableAbac();

        $officer = $this->officer();
        $application = $this->approvedSubmission($officer);
        $this->approvedSubmission($this->officer()); // someone else's approved return

        $response = $this->actingAs($officer)->get(route('user.archive'));

        $response->assertOk();
        $response->assertSee('Completed Returns');
        $response->assertSee('Submission #' . $application->id);
        $response->assertSee(route('user.submissions.pdf', $application), false);
    }

    public function test_archive_shows_scoped_completed_returns_to_approver(): void
    {
        $this->disableAbac();

        $admin = $this->deskAdmin('LA');
        $inScope = $this->approvedSubmission($this->officer());

        $response = $this->actingAs($admin)->get(route('user.archive'));

        $response->assertOk();
        $response->assertSee('Submission #' . $inScope->id);
    }

    public function test_directorate_submission_report_shows_nis_logo(): void
    {
        $this->disableAbac();

        $user = User::factory()->create([
            'role' => 'directorate',
            'user_category' => 'directorate_user',
            'primary_location_type' => 'directorate',
            'primary_location_code' => 'HRM',
            'assigned_directorate_code' => 'HRM',
            'access_level' => 0,
        ]);

        $application = Application::create([
            'user_id' => $user->id,
            'status' => 'approved',
            'workflow_stage' => 'approved',
            'category' => 'directorate',
            'scope_code' => 'hrm',
            'return_data' => ['directorate_slug' => 'hrm', 'report_period' => '2026-08'],
        ]);

        $response = $this->actingAs($user)->get(route('user.directorates.submissions.show', $application));

        $response->assertOk();
        $response->assertSee('nis-logo.png', false);
    }
}
