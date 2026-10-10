<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReturnDataExplorerTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_view_data_explorer(): void
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

        $response = $this->actingAs($superAdmin)->get(route('superadmin.data-explorer'));

        $response->assertOk();
        $response->assertViewIs('super-admin.data-explorer');
        $response->assertSee('Return Data Explorer');
    }

    public function test_cgis_user_can_view_data_explorer(): void
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

        $response = $this->actingAs($cgisUser)->get(route('cgis.data-explorer'));

        $response->assertOk();
        $response->assertViewIs('super-admin.data-explorer');
        $response->assertSee('Return Data Explorer');
    }

    public function test_filter_by_category_returns_only_matching_applications(): void
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

        $user = User::factory()->create();

        Application::factory()->create(['user_id' => $user->id, 'category' => 'state', 'scope_code' => 'LA']);
        Application::factory()->create(['user_id' => $user->id, 'category' => 'directorate', 'scope_code' => 'passport']);
        Application::factory()->create(['user_id' => $user->id, 'category' => 'cgis', 'scope_code' => 'actu']);

        $response = $this->actingAs($superAdmin)->get(route('superadmin.data-explorer', ['category' => 'directorate']));

        $response->assertOk();
        $response->assertViewHas('records', fn ($records) => $records->count() === 1 && $records->first()->category === 'directorate');
    }

    public function test_free_text_search_finds_return_by_reporting_officer(): void
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

        $user = User::factory()->create();

        $matching = Application::factory()->create([
            'user_id' => $user->id,
            'category' => 'state',
            'scope_code' => 'LA',
            'return_data' => ['reporting_officer' => 'Inspector John Doe'],
        ]);

        Application::factory()->create([
            'user_id' => $user->id,
            'category' => 'state',
            'scope_code' => 'AB',
            'return_data' => ['reporting_officer' => 'Jane Smith'],
        ]);

        $response = $this->actingAs($superAdmin)->get(route('superadmin.data-explorer', ['search' => 'John Doe']));

        $response->assertOk();
        $response->assertViewHas('records', fn ($records) => $records->count() === 1 && $records->first()->id === $matching->id);
    }

    public function test_data_field_filter_finds_matching_records(): void
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

        $user = User::factory()->create();

        $matching = Application::factory()->create([
            'user_id' => $user->id,
            'category' => 'state',
            'scope_code' => 'LA',
            'return_data' => [
                'hrm' => ['cadre' => ['comptroller' => ['male' => 10, 'female' => 5]]],
            ],
        ]);

        Application::factory()->create([
            'user_id' => $user->id,
            'category' => 'state',
            'scope_code' => 'AB',
            'return_data' => [
                'hrm' => ['cadre' => ['comptroller' => ['male' => 0, 'female' => 2]]],
            ],
        ]);

        $response = $this->actingAs($superAdmin)->get(route('superadmin.data-explorer', [
            'field_path' => 'hrm.cadre.comptroller.male',
            'field_operator' => '>',
            'field_value' => '0',
        ]));

        $response->assertOk();
        $response->assertViewHas('records', fn ($records) => $records->count() === 1 && $records->first()->id === $matching->id);
    }

    public function test_csv_export_returns_downloadable_response(): void
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

        $user = User::factory()->create();

        Application::factory()->create([
            'user_id' => $user->id,
            'category' => 'state',
            'scope_code' => 'LA',
            'return_data' => [
                'report_period' => '2026-01',
                'reporting_officer' => 'John Doe',
                'command_name' => 'Lagos Command',
            ],
        ]);

        $response = $this->actingAs($superAdmin)->get(route('superadmin.data-explorer', ['export' => 'csv']));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=utf-8');
        $response->assertSee('Return ID');
        $response->assertSee('John Doe');
    }
}
