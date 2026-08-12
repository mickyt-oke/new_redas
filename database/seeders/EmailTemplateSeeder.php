<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Seed required email templates.
     */
    public function run(): void
    {
        EmailTemplate::updateOrCreate(
            ['key' => 'verify_email_magic_link'],
            [
                'type' => 'workflow',
                'subject' => 'Verify your NIS-REDAS account',
                'body' => <<<HTML
<p>Hello {{ name }},</p>
<p>Your NIS-REDAS account has been created successfully.</p>
<p>Please verify your email by clicking the link below:</p>
<p><a href="{{ magic_link }}">Verify Email</a></p>
<p>This link expires in {{ expires_in_minutes }} minutes.</p>
<p>If you did not initiate this request, please ignore this email.</p>
HTML,
                'is_active' => true,
            ]
        );

        // Add or create password reset magic link template
        EmailTemplate::updateOrCreate(
            ['key' => 'password_reset_magic_link'],
            [
                'type' => 'workflow',
                'subject' => 'Reset your NIS-REDAS password',
                'body' => <<<HTML
<p>Hello {{ name }},</p>
<p>We received a request to reset your NIS-REDAS password.</p>
<p>Please reset your password by clicking the link below:</p>
<p><a href="{{ magic_link }}">Reset Password</a></p>
<p>This link expires in {{ expires_in_minutes }} minutes.</p>
<p>If you did not initiate this request, please ignore this email.</p>
HTML,
                'is_active' => true,
            ]
        );
    }
}
