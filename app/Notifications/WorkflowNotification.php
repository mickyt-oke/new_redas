<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class WorkflowNotification extends Notification
{
    use Queueable, SerializesModels;

    public function __construct(
        protected string $templateKey,
        protected array $data = []
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $template = EmailTemplate::query()
            ->where('key', $this->templateKey)
            ->where('is_active', true)
            ->first();

        if (!$template) {
            throw new \Exception("Email template not found or inactive: {$this->templateKey}");
        }

        $subject = $this->replacePlaceholders($template->subject, $this->data);
        $body = $this->replacePlaceholders($template->body, $this->data);

        return (new MailMessage)
            ->subject($subject)
            ->html($body);
    }

    protected function replacePlaceholders(string $text, array $data): string
    {
        foreach ($data as $key => $value) {
            $text = str_replace('{{ ' . $key . ' }}', $value, $text);
        }

        return $text;
    }
}
