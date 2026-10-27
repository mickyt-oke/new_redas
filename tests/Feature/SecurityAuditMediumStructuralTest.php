<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\ApplicationComment;
use App\Models\User;
use App\Services\AuditLogger;
use App\Services\SubmissionWorkflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SecurityAuditMediumStructuralTest extends TestCase
{
    use RefreshDatabase;

    private function disableAbac(): void
    {
        \App\Models\Setting::updateOrCreate(['key' => 'abac_enabled'], ['value' => 'false']);
        \App\Models\Setting::updateOrCreate(['key' => 'abac_location_enforcement'], ['value' => 'false']);
    }

    public function test_application_soft_delete_retains_record_and_comments(): void
    {
        $this->disableAbac();

        $user = User::factory()->create([
            'user_category' => 'directorate_user',
            'primary_location_type' => 'directorate',
            'primary_location_code' => 'HRM',
            'role' => 'directorate',
            'access_level' => 0,
            'is_enabled' => true,
            'email_verified_at' => now(),
        ]);

        $application = SubmissionWorkflow::create($user, ['report_period' => '2025-05']);
        $application->delete();

        $this->assertSoftDeleted('applications', ['id' => $application->id]);
        $this->assertDatabaseHas('application_comments', ['application_id' => $application->id]);
    }

    public function test_user_deletion_is_restricted_does_not_cascade_returns(): void
    {
        $this->disableAbac();

        $user = User::factory()->create([
            'user_category' => 'directorate_user',
            'primary_location_type' => 'directorate',
            'primary_location_code' => 'HRM',
            'role' => 'directorate',
            'access_level' => 0,
            'is_enabled' => true,
            'email_verified_at' => now(),
        ]);

        $application = SubmissionWorkflow::create($user, ['report_period' => '2025-05']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        $user->delete();
    }

    public function test_audit_log_denormalises_actor_identity(): void
    {
        $user = User::factory()->create([
            'name' => 'Audit Actor',
            'email' => 'actor@nis.gov.ng',
            'user_category' => 'hq_admin',
        ]);

        AuditLogger::logCrud($user, 'test.action', 'user', 1);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'actor_name' => 'Audit Actor',
            'actor_email' => 'actor@nis.gov.ng',
            'actor_category' => 'hq_admin',
            'action' => 'test.action',
        ]);
    }

    public function test_user_location_codes_are_normalised_to_uppercase(): void
    {
        $user = User::factory()->create([
            'primary_location_code' => ' ab ',
            'assigned_state_code' => 'ab',
            'assigned_zonal_command_code' => 'zone-a',
            'assigned_cgis_unit_code' => 'actu ',
            'assigned_directorate_code' => ' hrm ',
            'geo_state' => ' ab ',
        ]);

        $this->assertSame('AB', $user->primary_location_code);
        $this->assertSame('AB', $user->assigned_state_code);
        $this->assertSame('ZONE-A', $user->assigned_zonal_command_code);
        $this->assertSame('ACTU', $user->assigned_cgis_unit_code);
        $this->assertSame('HRM', $user->assigned_directorate_code);
        $this->assertSame('AB', $user->geo_state);
    }

    public function test_legacy_admin_review_stage_is_removed(): void
    {
        $this->assertFalse(defined('App\Services\SubmissionWorkflow::STAGE_ADMIN_REVIEW'));
    }

    public function test_prune_retained_records_command_exists(): void
    {
        $this->artisan('redas:prune-retained-records', ['--days' => 0])
            ->assertSuccessful();
    }

    public function test_session_prune_command_exists(): void
    {
        $this->artisan('session:prune', ['--days' => 0])
            ->assertSuccessful();
    }
}
