<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\ApplicationComment;
use App\Models\User;
use App\Services\SubmissionWorkflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class WorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function disableAbac(): void
    {
        Cache::put('settings.abac_enabled', false);
        Cache::put('settings.abac_location_enforcement', false);
        Cache::put('settings.abac_hq_bypass', true);
    }

    private function stateUser(string $state = 'LA'): User
    {
        return User::factory()->create([
            'role' => 'officer',
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'primary_location_code' => $state,
            'access_level' => 0,
        ]);
    }

    private function deskAdmin(string $state = 'LA', string $zone = 'ZONE-A'): User
    {
        return User::factory()->create([
            'role' => 'state',
            'user_category' => 'desk_admin',
            'primary_location_type' => 'state',
            'primary_location_code' => $state,
            'assigned_zonal_command_code' => $zone,
            'access_level' => 1,
        ]);
    }

    private function zonalCommander(string $zone = 'ZONE-A'): User
    {
        return User::factory()->create([
            'role' => 'zonal',
            'user_category' => 'zonal_commander',
            'primary_location_type' => 'zonal',
            'primary_location_code' => $zone,
            'access_level' => 4,
        ]);
    }

    private function directorateUser(string $directorate = 'HRM'): User
    {
        return User::factory()->create([
            'role' => 'directorate',
            'user_category' => 'directorate_user',
            'primary_location_type' => 'directorate',
            'primary_location_code' => $directorate,
            'assigned_directorate_code' => $directorate,
            'access_level' => 0,
        ]);
    }

    private function directorateAdmin(string $directorate = 'HRM'): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'user_category' => 'directorate_admin',
            'primary_location_type' => 'directorate',
            'primary_location_code' => $directorate,
            'assigned_directorate_code' => $directorate,
            'access_level' => 3,
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

    public function test_state_return_flows_through_desk_zonal_and_hq_to_final_approval(): void
    {
        $this->disableAbac();

        $user = $this->stateUser('LA');
        $deskAdmin = $this->deskAdmin('LA', 'ZONE-A');
        $zonal = $this->zonalCommander('ZONE-A');
        $hqAdmin = $this->hqAdmin();

        $application = SubmissionWorkflow::create($user, ['report_period' => '2025-05']);
        $this->assertSame('desk_review', $application->workflow_stage);

        // Desk admin approves -> zonal review, zonal code stamped
        $this->actingAs($deskAdmin)->patch(route('desk.admin.submissions.approve', $application));
        $application->refresh();
        $this->assertSame('zonal_review', $application->workflow_stage);
        $this->assertSame('ZONE-A', $application->zonal_code);

        // Zonal commander approves -> HQ review
        $this->actingAs($zonal)->patch(route('zonal.submissions.approve', $application));
        $application->refresh();
        $this->assertSame('hq_review', $application->workflow_stage);

        // HQ admin approval is final
        $this->actingAs($hqAdmin)->patch(route('admin.submissions.approve', $application));
        $application->refresh();
        $this->assertSame('approved', $application->workflow_stage);
        $this->assertSame('approved', $application->status);
    }

    public function test_directorate_return_flows_through_directorate_admin_to_hq(): void
    {
        $this->disableAbac();

        $user = $this->directorateUser('HRM');
        $directorateAdmin = $this->directorateAdmin('HRM');
        $hqAdmin = $this->hqAdmin();

        $application = SubmissionWorkflow::create($user, ['report_period' => '2025-05']);
        $this->assertSame('directorate_review', $application->workflow_stage);
        $this->assertSame('directorate', $application->category);

        $this->actingAs($directorateAdmin)->patch(route('desk.admin.submissions.approve', $application));
        $application->refresh();
        $this->assertSame('hq_review', $application->workflow_stage);

        $this->actingAs($hqAdmin)->patch(route('admin.submissions.approve', $application));
        $application->refresh();
        $this->assertSame('approved', $application->status);
    }

    public function test_admin_and_super_admin_are_view_only(): void
    {
        $this->disableAbac();

        $user = $this->directorateUser('HRM');
        $application = SubmissionWorkflow::create($user, ['report_period' => '2025-05']);
        $application->update(['workflow_stage' => SubmissionWorkflow::STAGE_HQ_REVIEW]);

        // Neither the general admin nor the super admin has a review stage
        $this->assertNull(SubmissionWorkflow::stageForApprover($this->generalAdmin()));
        $this->assertNull(SubmissionWorkflow::stageForApprover($this->superAdmin()));

        // Their queues are empty
        $this->assertSame(0, SubmissionWorkflow::pendingQueryForApprover($this->generalAdmin())->count());
        $this->assertSame(0, SubmissionWorkflow::pendingQueryForApprover($this->superAdmin())->count());

        // The executive dashboard and returns register load for the super admin
        $this->actingAs($this->superAdmin())->get(route('superadmin.dashboard'))->assertOk();
        $this->actingAs($this->superAdmin())->get(route('superadmin.returns'))->assertOk();

        // The general admin can open read-only HQ pages but not user management
        $this->actingAs($this->generalAdmin())->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($this->generalAdmin())->get(route('admin.consolidation'))->assertOk();
        $this->actingAs($this->generalAdmin())->get(route('admin.users'))->assertForbidden();
    }

    public function test_rejection_requires_comment_and_records_history(): void
    {
        $this->disableAbac();

        $user = $this->directorateUser('HRM');
        $directorateAdmin = $this->directorateAdmin('HRM');

        $application = SubmissionWorkflow::create($user, ['report_period' => '2025-05']);

        // Comment is mandatory
        $this->actingAs($directorateAdmin)
            ->patch(route('desk.admin.submissions.reject', $application), [])
            ->assertSessionHasErrors('comment');

        $this->actingAs($directorateAdmin)
            ->patch(route('desk.admin.submissions.reject', $application), ['comment' => 'Incomplete figures.']);

        $application->refresh();
        $this->assertSame('returned', $application->status);
        $this->assertSame('Incomplete figures.', $application->comments);

        $comment = ApplicationComment::query()
            ->where('application_id', $application->id)
            ->where('action', ApplicationComment::ACTION_REJECTED)
            ->latest('id')
            ->first();

        $this->assertNotNull($comment);
        $this->assertSame('Incomplete figures.', $comment->comment);
        $this->assertSame($directorateAdmin->id, $comment->user_id);
        $this->assertSame(SubmissionWorkflow::STAGE_DIRECTORATE_REVIEW, $comment->stage);
    }

    public function test_approval_note_is_persisted_to_comment_history(): void
    {
        $this->disableAbac();

        $user = $this->directorateUser('HRM');
        $directorateAdmin = $this->directorateAdmin('HRM');

        $application = SubmissionWorkflow::create($user, ['report_period' => '2025-05']);

        $this->actingAs($directorateAdmin)
            ->patch(route('desk.admin.submissions.approve', $application), ['note' => 'Verified against source records.']);

        $this->assertDatabaseHas('application_comments', [
            'application_id' => $application->id,
            'user_id' => $directorateAdmin->id,
            'action' => ApplicationComment::ACTION_APPROVED,
            'comment' => 'Verified against source records.',
        ]);
    }

    public function test_owner_can_delete_pending_but_not_approved_return(): void
    {
        $this->disableAbac();

        $user = $this->directorateUser('HRM');
        $otherUser = $this->directorateUser('PRS');

        $application = SubmissionWorkflow::create($user, ['report_period' => '2025-05']);

        // Non-owner cannot delete
        $this->actingAs($otherUser)
            ->delete(route('user.directorates.submissions.destroy', $application))
            ->assertForbidden();

        // Owner can delete while pending
        $this->actingAs($user)
            ->delete(route('user.directorates.submissions.destroy', $application));
        $this->assertDatabaseMissing('applications', ['id' => $application->id]);

        // Approved returns are locked
        $locked = SubmissionWorkflow::create($user, ['report_period' => '2025-06']);
        $locked->update(['status' => 'approved', 'workflow_stage' => 'approved']);

        $this->actingAs($user)
            ->delete(route('user.directorates.submissions.destroy', $locked))
            ->assertForbidden();
        $this->assertDatabaseHas('applications', ['id' => $locked->id]);
    }

    public function test_desk_admin_only_sees_own_state_and_zonal_commander_own_zone(): void
    {
        $this->disableAbac();

        $lagosUser = $this->stateUser('LA');
        $kanoUser = $this->stateUser('KN');

        $lagosReturn = SubmissionWorkflow::create($lagosUser, ['report_period' => '2025-05']);
        $kanoReturn = SubmissionWorkflow::create($kanoUser, ['report_period' => '2025-05']);

        $lagosDesk = $this->deskAdmin('LA', 'ZONE-A');
        $kanoDesk = $this->deskAdmin('KN', 'ZONE-B');

        // Desk admin cannot approve another state's return
        $this->actingAs($lagosDesk)->patch(route('desk.admin.submissions.approve', $kanoReturn))
            ->assertForbidden();
        $this->actingAs($kanoDesk)->patch(route('desk.admin.submissions.approve', $lagosReturn))
            ->assertForbidden();

        // Push both to zonal review with their respective zones
        $this->actingAs($lagosDesk)->patch(route('desk.admin.submissions.approve', $lagosReturn));
        $this->actingAs($kanoDesk)->patch(route('desk.admin.submissions.approve', $kanoReturn));

        // Zonal commander of ZONE-A cannot act on ZONE-B's return
        $zoneACommander = $this->zonalCommander('ZONE-A');
        $this->actingAs($zoneACommander)->patch(route('zonal.submissions.approve', $kanoReturn->fresh()))
            ->assertForbidden();
        $this->actingAs($zoneACommander)->patch(route('zonal.submissions.approve', $lagosReturn->fresh()));
        $this->assertSame('hq_review', $lagosReturn->fresh()->workflow_stage);
    }

    public function test_workflow_events_create_notifications(): void
    {
        $this->disableAbac();

        $user = $this->directorateUser('HRM');
        $directorateAdmin = $this->directorateAdmin('HRM');

        $application = SubmissionWorkflow::create($user, ['report_period' => '2025-05']);

        // Reviewer notified of a new return awaiting their stage
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $directorateAdmin->id,
            'type' => 'workflow',
        ]);

        // Submitter notified on rejection
        $this->actingAs($directorateAdmin)
            ->patch(route('desk.admin.submissions.reject', $application), ['comment' => 'Fix the totals.']);

        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $user->id,
            'title' => 'Return returned for correction',
        ]);
    }
}
