<?php

namespace App\Services\Messaging;

use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Log;
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

        // Read via config() only: env() is unreliable once config is cached in
        // production, and config/services.php already sources this from env().
        $resendApiKey = config('services.resend.key');

        if (! is_string($resendApiKey) || trim($resendApiKey) === '') {
            throw new \RuntimeException('RESEND_API_KEY is not configured. Set it in your environment and clear config cache.');
        }

        try {
            Mail::mailer('resend')
                ->send([], [], function ($message) use ($toEmail, $toName, $subject, $bodyHtml) {
                    $message->to($toEmail, $toName);
                    $message->subject($subject);
                    $message->html($bodyHtml);
                });
        } catch (\Throwable $e) {
            Log::error('Resend email delivery failed', [
                'template' => $templateKey,
                'to' => $toEmail,
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException("Failed to send email for template [{$templateKey}]: {$e->getMessage()}", previous: $e);
        }
    }
}
