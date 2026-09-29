<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\User;
use App\Services\SubmissionWorkflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CgisUnitWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function disableAbac(): void
    {
        Cache::put('settings.abac_enabled', false);
        Cache::put('settings.abac_location_enforcement', false);
        Cache::put('settings.abac_hq_bypass', true);
    }

    private function cgisUnitUser(string $unit = 'actu'): User
    {
        return User::factory()->create([
            'role' => 'unit_officer',
            'user_category' => 'cgis_unit_user',
            'primary_location_type' => 'unit',
            'primary_location_code' => $unit,
            'assigned_cgis_unit_code' => $unit,
            'access_level' => 0,
        ]);
    }

    private function cgisDeskAdmin(string $unit = 'actu'): User
    {
        return User::factory()->create([
            'role' => 'unit_admin',
            'user_category' => 'cgis_desk_admin',
            'primary_location_type' => 'unit',
            'primary_location_code' => $unit,
            'assigned_cgis_unit_code' => $unit,
            'access_level' => 2,
        ]);
    }

    private function hqAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'user_category' => 'hq_admin',
            'primary_location_type' => 'headquarters',
            'primary_location_code' => 'HQ',
            'access_level' => 5,
        ]);
    }

    private function generalAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'user_category' => 'admin',
            'primary_location_type' => 'headquarters',
            'primary_location_code' => 'HQ',
            'access_level' => 5,
        ]);
    }

    private function superAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'user_category' => 'super_admin',
            'primary_location_type' => 'headquarters',
            'primary_location_code' => 'HQ',
            'access_level' => 6,
        ]);
    }

    public function test_cgis_unit_user_can_submit_return_via_unit_form(): void
    {
        $this->disableAbac();

        $user = $this->cgisUnitUser('actu');

        $response = $this->actingAs($user)->post(route('user.cgis-units.store', 'actu'), [
            'report_period' => now()->format('Y-m'),
            'reporting_officer' => $user->name,
            'data_consent' => '1',
        ]);

        $response->assertRedirect(route('user.cgis-units.show', 'actu'));

        $application = Application::query()->where('user_id', $user->id)->latest('id')->first();

        $this->assertNotNull($application);
        $this->assertSame('cgis', $application->category);
        $this->assertSame('cgis_desk_review', $application->workflow_stage);
        $this->assertSame('actu', $application->scope_code);
        $this->assertSame('actu', $application->return_data['cgis_unit_slug'] ?? null);
    }

    public function test_cgis_return_flows_through_desk_and_hq_approval(): void
    {
        $this->disableAbac();

        $user = $this->cgisUnitUser('provost');
        $deskAdmin = $this->cgisDeskAdmin('provost');
        $hqAdmin = $this->hqAdmin();
        $generalAdmin = $this->generalAdmin();

        $application = SubmissionWorkflow::create($user, ['report_period' => '2025-05']);

        $this->assertSame('cgis_desk_review', $application->workflow_stage);
        $this->assertSame('provost', $application->scope_code);
        $this->assertSame('cgis', $application->category);

        // The dedicated CGIS desk admin sees the return in the shared review queue
        $this->actingAs($deskAdmin)->get(route('user.desk.home'))->assertOk();

        // CGIS desk admin approves -> hq review
        $this->actingAs($deskAdmin)->patch(route('desk.admin.submissions.approve', $application));
        $application->refresh();
        $this->assertSame('hq_review', $application->workflow_stage);
        $this->assertSame('pending', $application->status);

        // HQ admin approval is final -> approved
        $this->actingAs($hqAdmin)->patch(route('admin.submissions.approve', $application));
        $application->refresh();
        $this->assertSame('approved', $application->workflow_stage);
        $this->assertSame('approved', $application->status);

        // General admin and super admin are view-only: no review routes for them
        $this->actingAs($generalAdmin)->patch(route('admin.submissions.approve', $application))
            ->assertForbidden();
    }

    public function test_cgis_desk_admin_cannot_approve_other_units_submission(): void
    {
        $this->disableAbac();

        $user = $this->cgisUnitUser('actu');
        $otherDeskAdmin = $this->cgisDeskAdmin('servicom');

        $application = SubmissionWorkflow::create($user, ['report_period' => '2025-05']);

        $this->actingAs($otherDeskAdmin)->patch(route('desk.admin.submissions.approve', $application))
            ->assertForbidden();
    }

    public function test_rejected_cgis_return_can_be_resubmitted_by_owner(): void
    {
        $this->disableAbac();

        $user = $this->cgisUnitUser('epms');
        $deskAdmin = $this->cgisDeskAdmin('epms');

        $application = SubmissionWorkflow::create($user, ['report_period' => '2025-05']);

        // Desk admin rejects with a mandatory comment
        $this->actingAs($deskAdmin)->patch(route('desk.admin.submissions.reject', $application), [
            'comment' => 'Please complete the appraisal section.',
        ]);
        $application->refresh();
        $this->assertSame('returned', $application->status);
        $this->assertSame('submitted', $application->workflow_stage);

        // Owner edits and resubmits -> back to cgis desk review
        $this->actingAs($user)->put(route('user.cgis-units.submissions.update', $application), [
            'report_period' => '2025-06',
            'reporting_officer' => $user->name,
            'data_consent' => '1',
        ]);

        $application->refresh();
        $this->assertSame('pending', $application->status);
        $this->assertSame('cgis_desk_review', $application->workflow_stage);
        $this->assertSame('epms', $application->return_data['cgis_unit_slug'] ?? null);
        $this->assertSame('resubmitted', collect($application->workflow_path)->last()['action'] ?? null);
    }

    public function test_cgis_routes_are_guarded_for_other_categories(): void
    {
        $this->disableAbac();

        $stateUser = User::factory()->create([
            'role' => 'officer',
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'primary_location_code' => 'LA',
            'access_level' => 0,
        ]);

        $this->actingAs($stateUser)->get('/user/cgis-units/dashboard')->assertForbidden();
        $this->actingAs($stateUser)->get('/user/cgis-units/actu')->assertForbidden();
        $this->actingAs($stateUser)->post(route('user.cgis-units.store', 'actu'), [])->assertForbidden();
    }

    public function test_cgis_unit_user_is_confined_to_own_unit_form(): void
    {
        $this->disableAbac();

        $user = $this->cgisUnitUser('actu');

        // Opening another unit's form redirects to the user's own unit
        $this->actingAs($user)->get('/user/cgis-units/servicom')
            ->assertRedirect('/user/cgis-units/actu');

        // Submitting to another unit's form is forbidden
        $this->actingAs($user)->post(route('user.cgis-units.store', 'servicom'), [
            'report_period' => now()->format('Y-m'),
            'reporting_officer' => $user->name,
            'data_consent' => '1',
        ])->assertForbidden();

        // Own unit form loads
        $this->actingAs($user)->get('/user/cgis-units/actu')->assertOk();
    }

    public function test_cgis_unit_dashboard_loads_for_unit_user(): void
    {
        $this->disableAbac();

        $user = $this->cgisUnitUser('servicom');

        $this->actingAs($user)->get('/user/cgis-units/dashboard')->assertOk();
    }

    public function test_legacy_headquarters_profile_cgis_user_keeps_access(): void
    {
        $this->disableAbac();

        // Accounts created before the canonical unit/unit_officer profile
        // existed must keep working during the transition.
        $legacyUser = User::factory()->create([
            'role' => 'officer',
            'user_category' => 'cgis_unit_user',
            'primary_location_type' => 'headquarters',
            'primary_location_code' => 'HQ',
            'assigned_cgis_unit_code' => 'actu',
            'access_level' => 0,
        ]);

        $this->actingAs($legacyUser)->get('/user/cgis-units/actu')->assertOk();

        $legacyDeskAdmin = User::factory()->create([
            'role' => 'admin',
            'user_category' => 'cgis_desk_admin',
            'primary_location_type' => 'headquarters',
            'primary_location_code' => 'HQ',
            'assigned_cgis_unit_code' => 'actu',
            'access_level' => 2,
        ]);

        $this->actingAs($legacyDeskAdmin)->get(route('user.desk.home'))->assertOk();
    }

    public function test_admin_created_cgis_accounts_use_unit_profile(): void
    {
        $this->disableAbac();

        $admin = $this->hqAdmin();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'EPMS Unit Officer',
            'service_number' => 'NIS/CGU/0101',
            'role' => 'unit_officer',
            'user_category' => 'cgis_unit_user',
            'primary_location_type' => 'unit',
            'primary_location_code' => 'epms',
            'assigned_cgis_unit_code' => 'epms',
            'email' => 'epms.officer@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('admin.users'));

        $unitOfficer = User::query()->where('email', 'epms.officer@example.com')->first();
        $this->assertNotNull($unitOfficer);
        $this->assertSame('unit_officer', $unitOfficer->role);
        $this->assertSame('unit', $unitOfficer->primary_location_type);
        $this->assertSame('epms', $unitOfficer->cgisUnitSlug());

        // The canonical profile passes the CGIS unit route middleware
        $this->actingAs($unitOfficer)->get('/user/cgis-units/epms')->assertOk();

        // Non-canonical combinations are rejected
        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Bad Combo',
            'service_number' => 'NIS/CGU/0102',
            'role' => 'officer',
            'user_category' => 'cgis_unit_user',
            'primary_location_type' => 'headquarters',
            'primary_location_code' => 'HQ',
            'assigned_cgis_unit_code' => 'epms',
            'email' => 'bad.combo@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('user_category');
    }
}
