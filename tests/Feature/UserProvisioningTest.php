<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProvisioningTest extends TestCase
{
    use RefreshDatabase;

    private function hqAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'user_category' => 'hq_admin',
            'primary_location_type' => 'headquarters',
            'primary_location_code' => 'HQ',
            'access_level' => 5,
        ]);
    }

    public function test_admin_create_user_page_shows_formations_from_json(): void
    {
        $admin = $this->hqAdmin();

        $response = $this->actingAs($admin)->get(route('admin.users.create'));

        $response->assertOk();
        $response->assertSee('Zone A');
        $response->assertSee('Seme Command');
        $response->assertSee('Lagos State Command');
        $response->assertSee('Zonal User');
    }

    public function test_admin_can_provision_zonal_user(): void
    {
        $admin = $this->hqAdmin();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Zonal Officer',
            'service_number' => 'NIS/ZON/1234',
            'role' => 'officer',
            'user_category' => 'zonal_user',
            'primary_location_type' => 'zonal',
            'formation_code' => 'ZONE_A',
            'email' => 'zonal@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'is_enabled' => '1',
        ]);

        $response->assertRedirect(route('admin.users'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', [
            'email' => 'zonal@example.com',
            'user_category' => 'zonal_user',
            'primary_location_type' => 'zonal',
            'primary_location_code' => 'ZONE_A',
            'geo_state' => 'LA',
        ]);
    }

    public function test_admin_can_provision_special_command_user(): void
    {
        $admin = $this->hqAdmin();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Seme Officer',
            'service_number' => 'NIS/SPE/1234',
            'role' => 'officer',
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'formation_code' => 'SEME',
            'email' => 'seme@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'is_enabled' => '1',
        ]);

        $response->assertRedirect(route('admin.users'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', [
            'email' => 'seme@example.com',
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'primary_location_code' => 'SEME',
            'geo_state' => 'LA',
        ]);
    }
}
