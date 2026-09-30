<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\SubmissionWorkflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class StateDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function disableAbac(): void
    {
        Cache::put('settings.abac_enabled', false);
        Cache::put('settings.abac_location_enforcement', false);
        Cache::put('settings.abac_hq_bypass', true);
    }

    private function stateUser(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'officer',
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'primary_location_code' => 'LA',
            'access_level' => 0,
        ], $overrides));
    }

    public function test_state_dashboard_shows_only_own_submissions_and_metrics(): void
    {
        $this->disableAbac();

        $userA = $this->stateUser();
        $userB = $this->stateUser();

        $appA1 = SubmissionWorkflow::create($userA, ['command_name' => 'Lagos', 'period' => '2026-08']);
        $appA2 = SubmissionWorkflow::create($userA, ['command_name' => 'Lagos', 'period' => '2026-09']);
        $appA2->forceFill(['status' => 'approved', 'workflow_stage' => 'approved'])->save();
        $appB = SubmissionWorkflow::create($userB, ['command_name' => 'Lagos', 'period' => '2025-12']);

        $response = $this->actingAs($userA)->get(route('user.dashboard'));

        $response->assertOk();
        $response->assertViewHas('submissions', function ($submissions) use ($appA1, $appA2, $appB) {
            $ids = $submissions->pluck('id');

            return $ids->contains($appA1->id)
                && $ids->contains($appA2->id)
                && ! $ids->contains($appB->id);
        });
        $response->assertViewHas('totalSubmissions', 2);
        $response->assertViewHas('approvedSubmissions', 1);
    }

    public function test_non_state_categories_cannot_access_state_dashboard_or_return_routes(): void
    {
        $this->disableAbac();

        $directorateUser = User::factory()->create([
            'role' => 'directorate',
            'user_category' => 'directorate_user',
            'primary_location_type' => 'directorate',
            'primary_location_code' => 'HRM',
            'access_level' => 0,
        ]);

        $this->actingAs($directorateUser)->get(route('user.dashboard'))->assertForbidden();
        $this->actingAs($directorateUser)->get(route('user.returns.create'))->assertForbidden();
        $this->actingAs($directorateUser)->post(route('user.returns.store'), [
            'command_name' => 'Lagos',
            'period' => '2026-09',
            'return_type' => 'monthly',
            'reporting_officer' => 'Intruder',
            'data_consent' => '1',
        ])->assertForbidden();

        $cgisUser = User::factory()->create([
            'role' => 'unit_officer',
            'user_category' => 'cgis_unit_user',
            'primary_location_type' => 'unit',
            'primary_location_code' => 'epms',
            'assigned_cgis_unit_code' => 'epms',
            'access_level' => 0,
        ]);

        $this->actingAs($cgisUser)->get(route('user.dashboard'))->assertForbidden();
        $this->actingAs($cgisUser)->get(route('user.returns.create'))->assertForbidden();
    }
}
