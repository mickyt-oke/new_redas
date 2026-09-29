<?php

namespace App\Notifications;

use App\Mail\WorkflowMail;
use App\Models\EmailTemplate;
use App\Services\Messaging\TemplateRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class WorkflowNotification extends Notification
{
    use Queueable, SerializesModels;

    public function __construct(
        protected string $templateKey,
        protected array $data = []
    ) {}

    /**
     * Only route through mail when an active template exists, so a missing or
     * disabled template never fails the queued job (which would not resolve
     * itself on retry) or interrupts the calling workflow action.
     */
    public function via($notifiable): array
    {
        if (! $this->template()) {
            Log::warning('Skipped workflow notification: template missing or inactive.', [
                'template_key' => $this->templateKey,
                'notifiable_id' => $notifiable->getKey(),
            ]);

            return [];
        }

        return ['mail'];
    }

    public function toMail($notifiable): WorkflowMail
    {
        $template = $this->template();

        if (! $template) {
            throw new \RuntimeException("Email template not found or inactive: {$this->templateKey}");
        }

        $renderer = new TemplateRenderer();
        $subject = $renderer->render((string) $template->subject, $this->data);
        $body = $renderer->render((string) $template->body, $this->data);

        return (new WorkflowMail($subject, $body))->to($notifiable->email, $notifiable->name);
    }

    protected function template(): ?EmailTemplate
    {
        return EmailTemplate::query()
            ->where('key', $this->templateKey)
            ->where('is_active', true)
            ->first();
    }
}
