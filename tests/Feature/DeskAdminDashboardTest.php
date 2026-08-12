<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class DeskAdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function disableAbac(): void
    {
        Cache::put('settings.abac_enabled', false);
        Cache::put('settings.abac_location_enforcement', false);
        Cache::put('settings.abac_hq_bypass', true);
    }

    public function test_desk_admin_dashboard_loads_without_applications_table(): void
    {
        $this->disableAbac();

        $user = User::factory()->create([
            'role' => 'state',
            'user_category' => 'desk_admin',
            'primary_location_type' => 'state',
            'primary_location_code' => 'LA',
            'access_level' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('user.desk.home'));

        $response->assertOk();
        $response->assertViewIs('desk-admin.dashboard');
    }

    public function test_directorate_admin_dashboard_loads_without_applications_table(): void
    {
        $this->disableAbac();

        $user = User::factory()->create([
            'role' => 'admin',
            'user_category' => 'directorate_admin',
            'primary_location_type' => 'directorate',
            'primary_location_code' => 'HRM',
            'assigned_directorate_code' => 'HRM',
            'access_level' => 3,
        ]);

        $response = $this->actingAs($user)->get(route('user.desk.home'));

        $response->assertOk();
        $response->assertViewIs('desk-admin.dashboard');
    }
}
