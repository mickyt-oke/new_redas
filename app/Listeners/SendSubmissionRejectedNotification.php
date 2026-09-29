<?php

namespace App\Listeners;

use App\Events\SubmissionRejected;
use App\Notifications\WorkflowNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendSubmissionRejectedNotification implements ShouldQueue
{
    public function handle(SubmissionRejected $event): void
    {
        $application = $event->application;
        $user = $application->user;

        $user->notify(new WorkflowNotification('submission_rejected', [
            'name' => $user->name,
            'comments' => $event->comments ?? 'No comments provided.',
        ]));
    }
}
