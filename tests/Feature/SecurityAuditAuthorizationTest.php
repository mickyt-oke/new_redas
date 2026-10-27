<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SecurityAuditAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::put('settings.abac_enabled', false);
        Cache::put('settings.abac_location_enforcement', false);
        Cache::put('settings.abac_hq_bypass', true);
    }

    private function hqAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'user_category' => 'hq_admin',
            'primary_location_type' => 'headquarters',
            'primary_location_code' => 'HQ',
            'service_number' => 'NIS/HQA/0001',
            'access_level' => 5,
            'is_enabled' => true,
            'email_verified_at' => now(),
        ]);
    }

    private function superAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'user_category' => 'super_admin',
            'primary_location_type' => 'headquarters',
            'primary_location_code' => 'HQ',
            'service_number' => 'NIS/SA/0001',
            'access_level' => 6,
            'is_enabled' => true,
            'email_verified_at' => now(),
        ]);
    }

    public function test_hq_admin_cannot_create_super_admin(): void
    {
        $hq = $this->hqAdmin();

        $response = $this->actingAs($hq)->post(route('admin.users.store'), [
            'name' => 'Escalated',
            'service_number' => 'NIS/ESC/9001',
            'role' => 'admin',
            'user_category' => 'super_admin',
            'primary_location_type' => 'headquarters',
            'email' => 'escalated@nis.gov.ng',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'is_enabled' => 1,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('users', ['email' => 'escalated@nis.gov.ng']);
    }

    public function test_hq_admin_cannot_self_elevate_to_super_admin(): void
    {
        $hq = $this->hqAdmin();

        $response = $this->actingAs($hq)->patch($this->signedUserRoute('admin.users.update', $hq), [
            'name' => $hq->name,
            'service_number' => $hq->service_number,
            'role' => 'admin',
            'user_category' => 'super_admin',
            'primary_location_type' => 'headquarters',
            'email' => $hq->email,
            'is_enabled' => 1,
        ]);

        $response->assertForbidden();
        $this->assertSame('hq_admin', $hq->fresh()->user_category);
    }

    public function test_hq_admin_cannot_toggle_status_of_super_admin(): void
    {
        $hq = $this->hqAdmin();
        $super = $this->superAdmin();

        $response = $this->actingAs($hq)->patch($this->signedUserRoute('admin.users.toggle_status', $super));

        $response->assertForbidden();
        $this->assertTrue((bool) $super->fresh()->is_enabled);
    }

    public function test_cgis_unit_user_is_blocked_from_national_explorer(): void
    {
        $user = User::factory()->create([
            'role' => 'unit_officer',
            'user_category' => 'cgis_unit_user',
            'primary_location_type' => 'unit',
            'primary_location_code' => 'actu',
            'assigned_cgis_unit_code' => 'actu',
            'access_level' => 0,
            'is_enabled' => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)->get(route('cgis.data-explorer'))->assertForbidden();
        $this->actingAs($user)->get(route('cgis.data-explorer', ['export' => 'csv']))->assertForbidden();
    }

    public function test_reviewer_accounts_require_a_formation_scope(): void
    {
        $hq = $this->hqAdmin();

        $response = $this->actingAs($hq)->post(route('admin.users.store'), [
            'name' => 'Blank Desk Admin',
            'service_number' => 'NIS/BDA/0001',
            'role' => 'state',
            'user_category' => 'desk_admin',
            'primary_location_type' => 'state',
            'primary_location_code' => '',
            'formation_code' => '',
            'email' => 'blank.desk@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'is_enabled' => 1,
        ]);

        $response->assertSessionHasErrors(['formation_code']);
        $this->assertDatabaseMissing('users', ['email' => 'blank.desk@example.com']);
    }

    public function test_scopeless_desk_admin_cannot_review_submissions(): void
    {
        $deskAdmin = User::factory()->create([
            'role' => 'state',
            'user_category' => 'desk_admin',
            'primary_location_type' => 'state',
            'primary_location_code' => '',
            'assigned_state_code' => '',
            'access_level' => 1,
            'is_enabled' => true,
            'email_verified_at' => now(),
        ]);

        $officer = User::factory()->create();

        $application = Application::factory()->create([
            'user_id' => $officer->id,
            'workflow_stage' => 'desk_review',
            'category' => 'state',
            'scope_code' => 'LA',
            'status' => 'pending',
        ]);

        $this->actingAs($deskAdmin)
            ->get($this->applicationRoute('desk.admin.submissions.show', $application))
            ->assertForbidden();

        $this->actingAs($deskAdmin)
            ->patch($this->signedApplicationRoute('desk.admin.submissions.approve', $application))
            ->assertForbidden();

        $this->actingAs($deskAdmin)
            ->get($this->signedApplicationRoute('desk.admin.submissions.download', $application))
            ->assertForbidden();
    }

    public function test_scopeless_desk_admin_sees_zero_pending_submissions(): void
    {
        $deskAdmin = User::factory()->create([
            'role' => 'state',
            'user_category' => 'desk_admin',
            'primary_location_type' => 'state',
            'primary_location_code' => '',
            'assigned_state_code' => '',
            'access_level' => 1,
            'is_enabled' => true,
            'email_verified_at' => now(),
        ]);

        $officer = User::factory()->create();

        Application::factory()->count(3)->create([
            'user_id' => $officer->id,
            'workflow_stage' => 'desk_review',
            'category' => 'state',
            'scope_code' => 'LA',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($deskAdmin)->get(route('user.desk.home'));

        $response->assertOk();
        $response->assertViewHas('pendingSubmissions', fn ($submissions) => $submissions->isEmpty());
    }
}
