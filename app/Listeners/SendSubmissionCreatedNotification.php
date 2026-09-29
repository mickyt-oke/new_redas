<?php

namespace App\Listeners;

use App\Events\SubmissionCreated;
use App\Notifications\WorkflowNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendSubmissionCreatedNotification implements ShouldQueue
{
    public function handle(SubmissionCreated $event): void
    {
        $application = $event->application;
        $user = $application->user;

        if (! $user || ! $user->email) {
            return;
        }

        $user->notify(new WorkflowNotification('submission_submitted', [
            'name' => $user->name,
            'stage' => $application->workflow_stage,
        ]));
    }
}
