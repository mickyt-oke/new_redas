<?php

namespace Tests\Feature;

use App\Models\EmailTemplate;
use App\Models\User;
use App\Services\Messaging\ResendMailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ResendMailServiceTest extends TestCase
{
    use RefreshDatabase;

    private function seedTemplate(string $key = 'otp_login'): void
    {
        EmailTemplate::query()->create([
            'key' => $key,
            'type' => 'email',
            'subject' => 'Your code is {{ otp }}',
            'body' => 'Hi {{ name }}, code {{ otp }} expires in {{ expires_in_minutes }} minutes.',
            'is_active' => true,
        ]);
    }

    public function test_it_throws_when_template_is_missing(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/template not found or inactive/');

        (new ResendMailService())->sendFromTemplate('does_not_exist', [], 'user@example.com');
    }

    public function test_it_throws_when_template_is_inactive(): void
    {
        EmailTemplate::query()->create([
            'key' => 'disabled_template',
            'type' => 'email',
            'subject' => 'Subject',
            'body' => 'Body',
            'is_active' => false,
        ]);

        $this->expectException(\RuntimeException::class);

        (new ResendMailService())->sendFromTemplate('disabled_template', [], 'user@example.com');
    }

    public function test_it_throws_when_resend_api_key_is_not_configured(): void
    {
        $this->seedTemplate();
        Config::set('services.resend.key', null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/RESEND_API_KEY is not configured/');

        (new ResendMailService())->sendFromTemplate('otp_login', ['otp' => '1234', 'name' => 'Jane', 'expires_in_minutes' => 5], 'user@example.com');
    }

    public function test_it_sends_without_throwing_when_configured(): void
    {
        $this->seedTemplate();
        Config::set('services.resend.key', 're_test_key');
        Mail::fake();

        $user = User::factory()->create();

        // No exception should escape when the template and API key are both valid.
        (new ResendMailService())->sendFromTemplate(
            'otp_login',
            ['otp' => '1234', 'name' => $user->name, 'expires_in_minutes' => 5],
            $user->email,
            $user->name
        );

        $this->assertTrue(true);
    }
}
