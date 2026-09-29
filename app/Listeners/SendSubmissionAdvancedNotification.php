<?php

namespace App\Listeners;

use App\Events\SubmissionStageAdvanced;
use App\Notifications\WorkflowNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendSubmissionAdvancedNotification implements ShouldQueue
{
    public function handle(SubmissionStageAdvanced $event): void
    {
        $application = $event->application;
        $user = $application->user;

        $user->notify(new WorkflowNotification('submission_advanced', [
            'name' => $user->name,
            'stage' => $event->stage,
        ]));
    }
}
