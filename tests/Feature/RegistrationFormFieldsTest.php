<?php

namespace Tests\Feature;

use Tests\TestCase;

class RegistrationFormFieldsTest extends TestCase
{
    public function test_public_registration_form_is_not_available(): void
    {
        // The self-service registration form was removed. /register now redirects
        // visitors to the login page instead of exposing location/access fields.
        $this->get('/register')->assertRedirect('/login');
    }
}
