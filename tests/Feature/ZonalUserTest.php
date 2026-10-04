<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ZonalUserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::put('settings.abac_enabled', false);
        Cache::put('settings.abac_location_enforcement', false);
        Cache::put('settings.abac_hq_bypass', true);
    }

    private function zonalUser(string $code = 'ZONE_A'): User
    {
        return User::factory()->create([
            'role' => 'officer',
            'user_category' => 'zonal_user',
            'primary_location_type' => 'zonal',
            'primary_location_code' => $code,
            'geo_state' => 'LA',
            'access_level' => 0,
        ]);
    }

    private function zonalCommander(string $code = 'ZONE_A'): User
    {
        return User::factory()->create([
            'role' => 'zonal',
            'user_category' => 'zonal_commander',
            'primary_location_type' => 'zonal',
            'primary_location_code' => $code,
            'geo_state' => 'LA',
            'access_level' => 4,
        ]);
    }

    private function validReturnData(): array
    {
        return [
            'command_name' => 'Zone A — Ikeja, Lagos',
            'period' => '2026-08',
            'return_type' => 'monthly',
            'reporting_officer' => 'Test Officer',
            'data_consent' => '1',
            'personnel' => [
                ['cadre' => 'Comptroller', 'male' => '2', 'female' => '1', 'total' => '3'],
            ],
            'operations' => [
                'summary' => 'Routine patrols conducted.',
                'items' => [
                    ['activity' => 'Border patrol', 'count' => '5', 'remarks' => 'No incidents'],
                ],
            ],
            'logistics' => [
                ['item' => 'Vehicle', 'quantity' => '2', 'condition' => 'serviceable', 'remarks' => 'Operational'],
            ],
            'finance' => [
                'budget_allocated' => '100000',
                'expenditure' => '25000',
                'balance' => '75000',
            ],
            'challenges' => [
                'challenges' => 'Fuel shortage',
                'way_forward' => 'Request additional allocation',
            ],
        ];
    }

    public function test_zonal_dashboard_renders(): void
    {
        $user = $this->zonalUser();

        $response = $this->actingAs($user)->get(route('user.zones.dashboard'));

        $response->assertOk();
        $response->assertSee('Zone A');
    }

    public function test_zonal_user_can_create_return(): void
    {
        Storage::fake(config('filesystems.default'));
        $user = $this->zonalUser();

        $response = $this->actingAs($user)->post(route('user.zones.returns.store'), $this->validReturnData());

        $response->assertRedirect(route('user.zones.returns.index'));
        $response->assertSessionHasNoErrors();

        $application = Application::query()->sole();
        $this->assertSame($user->id, $application->user_id);
        $this->assertSame('zonal', $application->category);
        $this->assertSame('zonal_review', $application->workflow_stage);
        $this->assertSame('ZONE_A', $application->scope_code);
        $this->assertSame('2026-08', $application->return_data['report_period']);
    }

    public function test_zonal_user_can_update_and_delete_return(): void
    {
        Storage::fake(config('filesystems.default'));
        $user = $this->zonalUser();
        $application = Application::create([
            'user_id' => $user->id,
            'category' => 'zonal',
            'scope_code' => 'ZONE_A',
            'workflow_stage' => 'zonal_review',
            'status' => 'returned',
            'return_data' => array_merge($this->validReturnData(), ['report_period' => '2026-07']),
        ]);

        $response = $this->actingAs($user)->put($this->applicationRoute('user.zones.returns.update', $application), array_merge($this->validReturnData(), [
            'period' => '2026-09',
        ]));

        $response->assertRedirect(route('user.zones.returns.index'));
        $application->refresh();
        $this->assertSame('2026-09', $application->return_data['report_period']);
        $this->assertSame('pending', $application->status);

        $response = $this->actingAs($user)->delete($this->signedApplicationRoute('user.zones.returns.destroy', $application));
        $response->assertRedirect(route('user.zones.returns.index'));
        $this->assertNull(Application::find($application->id));
    }

    public function test_zonal_commander_can_approve_zonal_return(): void
    {
        $user = $this->zonalUser();
        $commander = $this->zonalCommander();
        $application = Application::create([
            'user_id' => $user->id,
            'category' => 'zonal',
            'scope_code' => 'ZONE_A',
            'workflow_stage' => 'zonal_review',
            'status' => 'pending',
            'return_data' => $this->validReturnData(),
        ]);

        $response = $this->actingAs($commander)->get(route('user.zonal.home'));
        $response->assertOk();
        $response->assertSee('#' . $application->id);

        $response = $this->actingAs($commander)->patch($this->signedApplicationRoute('zonal.submissions.approve', $application), [
            'note' => 'Approved for onward processing',
        ]);

        $response->assertSessionHasNoErrors();
        $application->refresh();
        $this->assertSame('hq_review', $application->workflow_stage);
    }
}
