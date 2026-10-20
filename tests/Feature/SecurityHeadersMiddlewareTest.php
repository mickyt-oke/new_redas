<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityHeadersMiddlewareTest extends TestCase
{
    public function test_security_headers_are_set_on_web_responses(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy');

        if (! app()->environment('local')) {
            $response->assertHeader('Strict-Transport-Security');
        }
    }
}
