<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Wraps pre-rendered HTML (from an EmailTemplate) for delivery via a
 * notification's mail channel, which only accepts Mailable or MailMessage
 * instances (MailMessage itself has no way to set raw HTML content).
 */
class WorkflowMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(string $mailSubject, string $html)
    {
        $this->subject($mailSubject);
        $this->html($html);
    }
}
