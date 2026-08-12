<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\User;
use App\Services\SubmissionWorkflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SubmissionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function disableAbac(): void
    {
        Cache::put('settings.abac_enabled', false);
        Cache::put('settings.abac_location_enforcement', false);
        Cache::put('settings.abac_hq_bypass', true);
    }

    public function test_state_return_flows_through_all_approval_stages(): void
    {
        $this->disableAbac();

        $stateUser = User::factory()->create([
            'role' => 'officer',
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'primary_location_code' => 'LA',
            'access_level' => 0,
        ]);

        $deskAdmin = User::factory()->create([
            'role' => 'state',
            'user_category' => 'desk_admin',
            'primary_location_type' => 'state',
            'primary_location_code' => 'LA',
            'assigned_zonal_command_code' => 'ZONE-A',
            'access_level' => 1,
        ]);

        $zonalCommander = User::factory()->create([
            'role' => 'zonal',
            'user_category' => 'zonal_commander',
            'primary_location_type' => 'zonal',
            'primary_location_code' => 'ZONE-A',
            'access_level' => 4,
        ]);

        $hqAdmin = User::factory()->create([
            'role' => 'admin',
            'user_category' => 'admin',
            'primary_location_type' => 'headquarters',
            'primary_location_code' => 'HQ',
            'access_level' => 5,
        ]);

        $superAdmin = User::factory()->create([
            'role' => 'admin',
            'user_category' => 'super_admin',
            'primary_location_type' => 'headquarters',
            'primary_location_code' => 'HQ',
            'access_level' => 6,
        ]);

        $application = SubmissionWorkflow::create($stateUser, ['command_name' => 'Lagos State']);

        $this->assertSame('desk_review', $application->workflow_stage);
        $this->assertSame('LA', $application->scope_code);
        $this->assertSame('state', $application->category);

        // Desk admin approves -> zonal review
        $this->actingAs($deskAdmin)->patch(route('desk.admin.submissions.approve', $application));
        $application->refresh();
        $this->assertSame('zonal_review', $application->workflow_stage);
        $this->assertSame('ZONE-A', $application->zonal_code);

        // Zonal commander approves -> hq review
        $this->actingAs($zonalCommander)->patch(route('zonal.submissions.approve', $application));
        $application->refresh();
        $this->assertSame('hq_review', $application->workflow_stage);

        // HQ admin approves -> admin review
        $this->actingAs($hqAdmin)->patch(route('admin.submissions.approve', $application));
        $application->refresh();
        $this->assertSame('admin_review', $application->workflow_stage);

        // Super admin approves -> approved
        $this->actingAs($superAdmin)->patch(route('superadmin.submissions.approve', $application));
        $application->refresh();
        $this->assertSame('approved', $application->workflow_stage);
        $this->assertSame('approved', $application->status);
    }

    public function test_directorate_return_flows_through_directorate_approval_chain(): void
    {
        $this->disableAbac();

        $directorateUser = User::factory()->create([
            'role' => 'directorate',
            'user_category' => 'directorate_user',
            'primary_location_type' => 'directorate',
            'primary_location_code' => 'HRM',
            'assigned_directorate_code' => 'HRM',
            'access_level' => 0,
        ]);

        $directorateAdmin = User::factory()->create([
            'role' => 'admin',
            'user_category' => 'directorate_admin',
            'primary_location_type' => 'directorate',
            'primary_location_code' => 'HRM',
            'assigned_directorate_code' => 'HRM',
            'access_level' => 3,
        ]);

        $hqAdmin = User::factory()->create([
            'role' => 'admin',
            'user_category' => 'admin',
            'primary_location_type' => 'headquarters',
            'primary_location_code' => 'HQ',
            'access_level' => 5,
        ]);

        $superAdmin = User::factory()->create([
            'role' => 'admin',
            'user_category' => 'super_admin',
            'primary_location_type' => 'headquarters',
            'primary_location_code' => 'HQ',
            'access_level' => 6,
        ]);

        $application = SubmissionWorkflow::create($directorateUser, ['report_period' => '2025-05']);

        $this->assertSame('directorate_review', $application->workflow_stage);
        $this->assertSame('hrm', $application->scope_code);
        $this->assertSame('directorate', $application->category);

        // Only the matching directorate admin can see it
        $this->actingAs($directorateAdmin)->get(route('user.desk.home'))->assertOk();

        $this->actingAs($directorateAdmin)->patch(route('desk.admin.submissions.approve', $application));
        $application->refresh();
        $this->assertSame('hq_review', $application->workflow_stage);

        $this->actingAs($hqAdmin)->patch(route('admin.submissions.approve', $application));
        $application->refresh();
        $this->assertSame('admin_review', $application->workflow_stage);

        $this->actingAs($superAdmin)->patch(route('superadmin.submissions.approve', $application));
        $application->refresh();
        $this->assertSame('approved', $application->workflow_stage);
        $this->assertSame('approved', $application->status);
    }

    public function test_desk_admin_cannot_approve_out_of_state_submission(): void
    {
        $this->disableAbac();

        $stateUser = User::factory()->create([
            'role' => 'officer',
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'primary_location_code' => 'AB',
            'access_level' => 0,
        ]);

        $deskAdmin = User::factory()->create([
            'role' => 'state',
            'user_category' => 'desk_admin',
            'primary_location_type' => 'state',
            'primary_location_code' => 'LA',
            'access_level' => 1,
        ]);

        $application = SubmissionWorkflow::create($stateUser, ['command_name' => 'Abuja State']);

        $this->actingAs($deskAdmin)->patch(route('desk.admin.submissions.approve', $application))
            ->assertForbidden();
    }
}
