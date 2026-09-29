<?php

namespace Tests\Feature;

use App\Events\SubmissionCreated;
use App\Events\SubmissionRejected;
use App\Events\SubmissionStageAdvanced;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Notifications\WorkflowNotification;
use App\Services\SubmissionWorkflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class WorkflowMessagingTest extends TestCase
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

    private function seedWorkflowTemplates(): void
    {
        foreach (['submission_submitted', 'submission_advanced', 'submission_rejected'] as $key) {
            EmailTemplate::query()->create([
                'key' => $key,
                'type' => 'workflow',
                'subject' => 'Subject for ' . $key,
                'body' => 'Hello {{ name }}, stage={{ stage }} comments={{ comments }}',
                'is_active' => true,
            ]);
        }
    }

    public function test_submission_creation_dispatches_submission_created_event(): void
    {
        $this->disableAbac();
        Event::fake([SubmissionCreated::class]);

        $user = $this->stateUser();
        $application = SubmissionWorkflow::create($user, ['report_period' => '2025-05']);

        Event::assertDispatched(SubmissionCreated::class, function ($event) use ($application) {
            return $event->application->is($application);
        });
    }

    public function test_approval_dispatches_submission_stage_advanced_event(): void
    {
        $this->disableAbac();

        $user = $this->stateUser('LA');
        $deskAdmin = $this->deskAdmin('LA', 'ZONE-A');
        $application = SubmissionWorkflow::create($user, ['report_period' => '2025-05']);

        Event::fake([SubmissionStageAdvanced::class]);

        SubmissionWorkflow::approve($application, $deskAdmin);

        Event::assertDispatched(SubmissionStageAdvanced::class, function ($event) use ($application) {
            return $event->application->is($application) && $event->stage === 'zonal_review';
        });
    }

    public function test_rejection_dispatches_submission_rejected_event_with_comment(): void
    {
        $this->disableAbac();

        $user = $this->directorateUser('HRM');
        $application = SubmissionWorkflow::create($user, ['report_period' => '2025-05']);

        Event::fake([SubmissionRejected::class]);

        $directorateAdmin = User::factory()->create([
            'role' => 'admin',
            'user_category' => 'directorate_admin',
            'primary_location_type' => 'directorate',
            'primary_location_code' => 'HRM',
            'assigned_directorate_code' => 'HRM',
            'access_level' => 3,
        ]);

        SubmissionWorkflow::reject($application, $directorateAdmin, 'Missing figures.');

        Event::assertDispatched(SubmissionRejected::class, function ($event) use ($application) {
            return $event->application->is($application) && $event->comments === 'Missing figures.';
        });
    }

    public function test_submitter_receives_workflow_notification_email_on_creation(): void
    {
        $this->disableAbac();
        $this->seedWorkflowTemplates();
        Notification::fake();

        $user = $this->stateUser();
        SubmissionWorkflow::create($user, ['report_period' => '2025-05']);

        Notification::assertSentTo($user, WorkflowNotification::class);
    }

    public function test_submitter_receives_workflow_notification_email_on_approval_and_rejection(): void
    {
        $this->disableAbac();
        $this->seedWorkflowTemplates();

        $user = $this->stateUser('LA');
        $deskAdmin = $this->deskAdmin('LA', 'ZONE-A');
        $application = SubmissionWorkflow::create($user, ['report_period' => '2025-05']);

        Notification::fake();

        SubmissionWorkflow::approve($application, $deskAdmin);
        Notification::assertSentTo($user, WorkflowNotification::class);

        SubmissionWorkflow::reject($application, $deskAdmin, 'Incomplete data.');
        Notification::assertSentTo($user, WorkflowNotification::class);
    }

    /**
     * The workflow must notify the actual owner of the submission regardless
     * of which user category originated it (state, directorate, or CGIS unit).
     */
    public function test_workflow_notifications_resolve_the_correct_owner_across_user_categories(): void
    {
        $this->disableAbac();
        $this->seedWorkflowTemplates();

        $stateUser = $this->stateUser('KN');
        $directorateUser = $this->directorateUser('ICT');
        $cgisUser = $this->cgisUnitUser('actu');

        Notification::fake();

        SubmissionWorkflow::create($stateUser, ['report_period' => '2025-05']);
        SubmissionWorkflow::create($directorateUser, ['report_period' => '2025-05']);
        SubmissionWorkflow::create($cgisUser, ['report_period' => '2025-05']);

        Notification::assertSentTo($stateUser, WorkflowNotification::class);
        Notification::assertSentTo($directorateUser, WorkflowNotification::class);
        Notification::assertSentTo($cgisUser, WorkflowNotification::class);
    }

    public function test_workflow_notification_is_skipped_without_exception_when_template_missing(): void
    {
        $this->disableAbac();
        // Intentionally do not seed email templates.

        $user = $this->stateUser();

        // Should not throw even though no active 'submission_submitted' template exists.
        $application = SubmissionWorkflow::create($user, ['report_period' => '2025-05']);

        $this->assertNotNull($application->id);

        $notification = new WorkflowNotification('submission_submitted', ['name' => $user->name, 'stage' => 'desk_review']);
        $this->assertSame([], $notification->via($user));
    }

    public function test_workflow_notification_via_mail_when_template_active(): void
    {
        $this->seedWorkflowTemplates();

        $user = $this->stateUser();
        $notification = new WorkflowNotification('submission_submitted', ['name' => $user->name, 'stage' => 'desk_review']);

        $this->assertSame(['mail'], $notification->via($user));

        $mail = $notification->toMail($user);
        $this->assertSame('Subject for submission_submitted', $mail->subject);
    }
}
