<?php

namespace App\Services\Messaging;

use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Mail;

class ResendMailService
{
    public function sendFromTemplate(
        string $templateKey,
        array $variables,
        string $toEmail,
        ?string $toName = null
    ): void {
        /** @var EmailTemplate|null $tpl */
        $tpl = EmailTemplate::query()
            ->where('key', $templateKey)
            ->where('is_active', true)
            ->first();

        if (! $tpl) {
            throw new \RuntimeException("Email template not found or inactive: {$templateKey}");
        }

        $renderer = new TemplateRenderer();
        $subject = $renderer->render((string) $tpl->subject, $variables);

        // Resend accepts html/text; we’ll treat body as HTML.
        $bodyHtml = $renderer->render((string) $tpl->body, $variables);

        $resendApiKey = config('mail.mailers.resend.api_key')
            ?? config('services.resend.key', null)
            ?? env('RESEND_API_KEY');

        if (empty($resendApiKey)) {
            $resendApiKey = env('RESEND_API_KEY');
        }

        if (! is_string($resendApiKey) || trim($resendApiKey) === '') {
            throw new \RuntimeException('RESEND_API_KEY is not configured. Set it in your environment and clear config cache.');
        }

        Mail::mailer('resend')
            ->send([], [], function ($message) use ($toEmail, $toName, $subject, $bodyHtml) {
                $message->to($toEmail, $toName);
                $message->subject($subject);
                $message->html($bodyHtml);
            });
    }
}
