<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DirectorateDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_directorate_user_can_visit_dashboard(): void
    {
        $user = User::factory()->create([
            'name' => 'HRM Officer',
            'role' => 'directorate',
            'user_category' => 'directorate_user',
            'primary_location_type' => 'directorate',
            'primary_location_code' => 'HRM',
            'assigned_directorate_code' => 'HRM',
            'access_level' => 0,
        ]);

        $response = $this->actingAs($user)->get(route('user.directorates.dashboard'));

        $response->assertOk();
        $response->assertViewIs('user.directorates.dashboard');
        $response->assertSee('Human Resources Management');
        $response->assertSee('HRM Officer');
    }

    public function test_directorate_user_is_redirected_to_their_own_directorate_form(): void
    {
        $user = User::factory()->create([
            'role' => 'directorate',
            'user_category' => 'directorate_user',
            'primary_location_type' => 'directorate',
            'primary_location_code' => 'PRS',
            'assigned_directorate_code' => 'PRS',
            'access_level' => 0,
        ]);

        $response = $this->actingAs($user)->get(route('user.directorates.show', ['slug' => 'hrm']));

        $response->assertRedirect(route('user.directorates.show', ['slug' => 'prs']));
    }

    public function test_directorate_admin_can_access_any_directorate_form(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'user_category' => 'directorate_admin',
            'primary_location_type' => 'directorate',
            'primary_location_code' => 'HRM',
            'assigned_directorate_code' => 'HRM',
            'access_level' => 3,
        ]);

        $response = $this->actingAs($user)->get(route('user.directorates.show', ['slug' => 'finance']));

        $response->assertOk();
        $response->assertViewIs('user.directorates.finance');
    }
}
