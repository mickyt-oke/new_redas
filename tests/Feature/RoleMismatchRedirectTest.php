<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class RoleMismatchRedirectTest extends TestCase
{
    use RefreshDatabase;

    private function disableAbac(): void
    {
        Cache::put('settings.abac_enabled', false);
        Cache::put('settings.abac_location_enforcement', false);
        Cache::put('settings.abac_hq_bypass', true);
    }

    public function test_state_user_is_redirected_from_admin_area(): void
    {
        $this->disableAbac();

        $user = User::factory()->create([
            'role' => 'officer',
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'primary_location_code' => 'LA',
            'access_level' => 0,
        ]);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('user.dashboard'));
    }

    public function test_view_only_admin_is_redirected_from_hq_only_pages(): void
    {
        $this->disableAbac();

        $user = User::factory()->create([
            'role' => 'admin',
            'user_category' => 'admin',
            'primary_location_type' => 'headquarters',
            'access_level' => 5,
        ]);

        $this->actingAs($user)
            ->get(route('admin.users'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_directorate_user_is_redirected_from_state_pages(): void
    {
        $this->disableAbac();

        $user = User::factory()->create([
            'role' => 'directorate',
            'user_category' => 'directorate_user',
            'primary_location_type' => 'directorate',
            'primary_location_code' => 'HRM',
            'access_level' => 0,
        ]);

        $this->actingAs($user)
            ->get(route('user.dashboard'))
            ->assertRedirect(route('user.directorates.dashboard'));
    }

    public function test_user_is_not_redirected_from_their_own_pages(): void
    {
        $this->disableAbac();

        $user = User::factory()->create([
            'role' => 'officer',
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'primary_location_code' => 'LA',
            'access_level' => 0,
        ]);

        $this->actingAs($user)
            ->get(route('user.dashboard'))
            ->assertOk();
    }
}
