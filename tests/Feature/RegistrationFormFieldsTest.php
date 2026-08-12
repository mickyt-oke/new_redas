<?php

namespace Tests\Feature;

use Tests\TestCase;

class RegistrationFormFieldsTest extends TestCase
{
    public function test_registration_form_exposes_location_and_access_profile_fields(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Primary Location Code');
        $response->assertSee('name="geo_state"', false);
        $response->assertSee('name="user_category"', false);
        $response->assertSee('name="primary_location_type"', false);
    }
}
