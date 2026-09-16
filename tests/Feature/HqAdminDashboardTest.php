<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class HqAdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function disableAbac(): void
    {
        Cache::put('settings.abac_enabled', false);
        Cache::put('settings.abac_location_enforcement', false);
        Cache::put('settings.abac_hq_bypass', true);
    }

    private function hqAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'user_category' => 'admin',
            'primary_location_type' => 'headquarters',
            'access_level' => 5,
        ]);
    }

    public function test_hq_admin_dashboard_loads(): void
    {
        $this->disableAbac();

        $response = $this->actingAs($this->hqAdmin())->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewIs('admin.headquarters.index');
    }

    public function test_hq_admin_returns_archive_analytics_and_reports_pages_load(): void
    {
        $this->disableAbac();
        $admin = $this->hqAdmin();

        $this->actingAs($admin)->get(route('admin.hq.returns'))->assertOk()->assertViewIs('admin.headquarters.returns');
        $this->actingAs($admin)->get(route('admin.hq.archive'))->assertOk()->assertViewIs('admin.headquarters.archive');
        $this->actingAs($admin)->get(route('admin.hq.analytics'))->assertOk()->assertViewIs('admin.headquarters.analytics');
        $this->actingAs($admin)->get(route('admin.hq.reports'))->assertOk()->assertViewIs('admin.headquarters.reports');
    }

    public function test_directorate_admin_cannot_access_hq_dashboard(): void
    {
        $this->disableAbac();

        $user = User::factory()->create([
            'role' => 'admin',
            'user_category' => 'directorate_admin',
            'primary_location_type' => 'directorate',
            'primary_location_code' => 'HRM',
            'assigned_directorate_code' => 'HRM',
            'access_level' => 3,
        ]);

        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_hq_admin_can_view_a_return_detail(): void
    {
        $this->disableAbac();
        $admin = $this->hqAdmin();

        $officer = User::factory()->create([
            'role' => 'directorate',
            'user_category' => 'directorate_user',
            'primary_location_type' => 'directorate',
            'assigned_directorate_code' => 'HRM',
        ]);

        $application = Application::create([
            'user_id' => $officer->id,
            'status' => 'pending',
            'workflow_stage' => 'hq_review',
            'workflow_path' => [
                ['stage' => 'submitted', 'by' => $officer->id, 'at' => now()->subDay()->toDateTimeString()],
                ['stage' => 'hq_review', 'by' => $admin->id, 'at' => now()->toDateTimeString(), 'action' => 'approved'],
            ],
            'category' => 'directorate',
            'scope_code' => 'hrm',
            'return_data' => [
                'report_period' => now()->format('Y-m'),
                'reporting_officer' => $officer->name,
                'directorate_slug' => 'hrm',
            ],
        ]);

        $response = $this->actingAs($admin)->get(route('admin.hq.returns.show', $application));

        $response->assertOk();
        $response->assertViewIs('admin.headquarters.return-show');
    }

    public function test_hq_admin_can_generate_a_quarterly_excel_report(): void
    {
        $this->disableAbac();
        $admin = $this->hqAdmin();

        $officer = User::factory()->create([
            'role' => 'directorate',
            'user_category' => 'directorate_user',
            'primary_location_type' => 'directorate',
            'assigned_directorate_code' => 'HRM',
        ]);

        Application::create([
            'user_id' => $officer->id,
            'status' => 'approved',
            'workflow_stage' => 'approved',
            'workflow_path' => [
                ['stage' => 'submitted', 'by' => $officer->id, 'at' => now()->toDateTimeString()],
            ],
            'category' => 'directorate',
            'scope_code' => 'hrm',
            'return_data' => [
                'report_period' => now()->format('Y-m'),
                'reporting_officer' => $officer->name,
                'directorate_slug' => 'hrm',
            ],
        ]);

        $response = $this->actingAs($admin)->post(route('admin.hq.reports.generate'), [
            'report_type' => 'quarterly',
            'year' => now()->year,
            'part' => (int) ceil(now()->month / 3),
        ]);

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
