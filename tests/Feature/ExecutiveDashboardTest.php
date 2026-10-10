<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\User;
use App\Services\ExecutiveDashboardAggregator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExecutiveDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_view_executive_dashboard(): void
    {
        $superAdmin = User::factory()->create([
            'user_category' => 'super_admin',
            'role' => 'admin',
            'primary_location_type' => 'headquarters',
            'primary_location_code' => 'HQ',
            'access_level' => 6,
            'is_enabled' => true,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($superAdmin)->get(route('superadmin.dashboard'));

        $response->assertOk();
        $response->assertViewIs('super-admin.executive-dashboard');
        $response->assertSee('Executive Dashboard (CGIS)');
    }

    public function test_cgis_user_can_view_executive_dashboard(): void
    {
        $cgisUser = User::factory()->create([
            'user_category' => 'cgis_unit_user',
            'role' => 'unit_officer',
            'primary_location_type' => 'unit',
            'primary_location_code' => 'actu',
            'assigned_cgis_unit_code' => 'actu',
            'access_level' => 0,
            'is_enabled' => true,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($cgisUser)->get(route('cgis.executive.dashboard'));

        $response->assertOk();
        $response->assertViewIs('super-admin.executive-dashboard');
        $response->assertSee('Executive Dashboard (CGIS)');
    }

    public function test_aggregator_sums_hrm_personnel(): void
    {
        $user = User::factory()->create([
            'user_category' => 'state_user',
            'role' => 'officer',
            'primary_location_type' => 'state',
            'primary_location_code' => 'LA',
            'geo_state' => 'LA',
            'is_enabled' => true,
        ]);

        Application::factory()->create([
            'user_id' => $user->id,
            'category' => 'state',
            'scope_code' => 'LA',
            'status' => 'approved',
            'return_data' => [
                'hrm' => [
                    'cadre' => [
                        'comptroller' => ['male' => 10, 'female' => 5],
                        'superintendent' => ['male' => 20, 'female' => 15],
                    ],
                    'rank' => [
                        'acg' => ['male' => 2, 'female' => 1],
                        'cis' => ['male' => 3, 'female' => 2],
                    ],
                ],
            ],
        ]);

        $aggregator = app(ExecutiveDashboardAggregator::class);
        $result = $aggregator->aggregate();

        $this->assertSame(58, $result['personnel']['total']);
        $this->assertSame(35, $result['personnel']['male']);
        $this->assertSame(23, $result['personnel']['female']);
        $this->assertSame(15, $result['personnel']['cadre']['comptroller']['total']);
        $this->assertSame(35, $result['personnel']['cadre']['superintendent']['total']);
        $this->assertSame(3, $result['personnel']['rank']['acg']['total']);
        $this->assertSame(5, $result['personnel']['rank']['cis']['total']);
    }

    public function test_aggregator_sums_passport_facilities(): void
    {
        $user = User::factory()->create([
            'user_category' => 'directorate_user',
            'role' => 'directorate',
            'primary_location_type' => 'directorate',
            'primary_location_code' => 'passport',
            'assigned_directorate_code' => 'passport',
            'is_enabled' => true,
        ]);

        Application::factory()->create([
            'user_id' => $user->id,
            'category' => 'directorate',
            'scope_code' => 'passport',
            'status' => 'approved',
            'return_data' => [
                'passport' => [
                    'standard_passport' => [
                        ['32p' => 100, '64p' => 50],
                        ['32p' => 20, '64p' => 10],
                    ],
                    'official_passport' => [
                        ['32p' => 30, '64p' => 15],
                    ],
                    'ecowas_enbic' => [
                        ['32p' => 5, '64p' => 5],
                    ],
                ],
            ],
        ]);

        $result = app(ExecutiveDashboardAggregator::class)->aggregate();

        $this->assertSame(235, $result['facilities']['passport_books']);
        $this->assertSame(180, $result['facilities']['passport_detail']['standard']);
        $this->assertSame(45, $result['facilities']['passport_detail']['official']);
        $this->assertSame(10, $result['facilities']['enbic']);
    }

    public function test_aggregator_sums_land_border_movement(): void
    {
        $user = User::factory()->create([
            'user_category' => 'state_user',
            'role' => 'officer',
            'primary_location_type' => 'state',
            'primary_location_code' => 'LA',
            'geo_state' => 'LA',
            'is_enabled' => true,
        ]);

        Application::factory()->create([
            'user_id' => $user->id,
            'category' => 'state',
            'scope_code' => 'LA',
            'status' => 'approved',
            'return_data' => [
                'border' => [
                    'land' => [
                        'lagos' => [
                            ['arrival_male' => 50, 'arrival_female' => 30, 'departure_male' => 40, 'departure_female' => 20],
                        ],
                    ],
                ],
            ],
        ]);

        $result = app(ExecutiveDashboardAggregator::class)->aggregate();

        $this->assertSame(80, $result['borders']['land']['arrivals']);
        $this->assertSame(60, $result['borders']['land']['departures']);
        $this->assertSame(90, $result['borders']['land']['male']);
        $this->assertSame(50, $result['borders']['land']['female']);
        $this->assertSame(80, $result['borders']['land_by_state']['Lagos']['arrivals']);
    }
}
