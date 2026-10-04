<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SpecialCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::put('settings.abac_enabled', false);
        Cache::put('settings.abac_location_enforcement', false);
        Cache::put('settings.abac_hq_bypass', true);
    }

    private function specialCommandUser(string $code = 'SEME'): User
    {
        return User::factory()->create([
            'role' => 'officer',
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'primary_location_code' => $code,
            'geo_state' => 'LA',
            'access_level' => 0,
        ]);
    }

    private function validReturnData(): array
    {
        return [
            'command_name' => 'Seme Command — Seme, Lagos (Land Border Special Command)',
            'period' => '2026-08',
            'return_type' => 'monthly',
            'reporting_officer' => 'Test Officer',
            'data_consent' => '1',
            'personnel' => [
                ['cadre' => 'Inspector', 'male' => '4', 'female' => '2', 'total' => '6'],
            ],
            'operations' => [
                'summary' => 'Border control operations.',
                'items' => [
                    ['activity' => 'Immigration checks', 'count' => '120', 'remarks' => 'Routine'],
                ],
            ],
            'logistics' => [
                ['item' => 'Barrier', 'quantity' => '3', 'condition' => 'serviceable', 'remarks' => 'In use'],
            ],
            'finance' => [
                'budget_allocated' => '50000',
                'expenditure' => '10000',
                'balance' => '40000',
            ],
            'challenges' => [
                'challenges' => 'Power supply',
                'way_forward' => 'Install solar inverter',
            ],
        ];
    }

    public function test_special_command_dashboard_renders(): void
    {
        $user = $this->specialCommandUser();

        $response = $this->actingAs($user)->get(route('special-commands.dashboard'));

        $response->assertOk();
        $response->assertSee('Seme Command');
    }

    public function test_special_command_user_can_submit_return(): void
    {
        Storage::fake(config('filesystems.default'));
        $user = $this->specialCommandUser();

        $response = $this->actingAs($user)->post(route('special-commands.returns.store'), $this->validReturnData());

        $response->assertRedirect(route('special-commands.returns.index'));
        $response->assertSessionHasNoErrors();

        $application = Application::query()->sole();
        $this->assertSame($user->id, $application->user_id);
        $this->assertSame('state', $application->category);
        $this->assertSame('desk_review', $application->workflow_stage);
        $this->assertSame('LA', $application->scope_code);
        $this->assertSame('SEME', $application->return_data['special_command_code']);
    }

    public function test_special_command_report_page_renders(): void
    {
        $user = $this->specialCommandUser();
        $application = Application::create([
            'user_id' => $user->id,
            'category' => 'state',
            'scope_code' => 'LA',
            'workflow_stage' => 'desk_review',
            'status' => 'pending',
            'return_data' => $this->validReturnData(),
        ]);

        $response = $this->actingAs($user)->get($this->applicationRoute('special-commands.returns.report', $application));

        $response->assertOk();
        $response->assertSee('Seme Command');
        $response->assertSee('Personnel Strength');
    }

    public function test_regular_state_user_cannot_access_special_command_area(): void
    {
        $user = User::factory()->create([
            'role' => 'officer',
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'primary_location_code' => 'LA',
            'geo_state' => 'LA',
            'access_level' => 0,
        ]);

        $response = $this->actingAs($user)->get(route('special-commands.dashboard'));

        $response->assertForbidden();
    }
}
