<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\User;
use App\Services\SubmissionWorkflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class StateCombinedFormTest extends TestCase
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

    public function test_combined_form_renders_all_ten_directorate_sections(): void
    {
        $this->disableAbac();

        $response = $this->actingAs($this->stateUser())->get(route('user.returns.create'));

        $response->assertOk();

        foreach (['hrm', 'prs', 'finance', 'investigation', 'passport', 'visa', 'migration', 'border', 'ict', 'works-logistics'] as $slug) {
            $response->assertSee('id="dir-'.$slug.'"', false);
        }

        // Spot-check one inner tab/section marker per directorate partial.
        foreach ([
            'id="tab-hrm-cadre"',
            'id="tab-prs-cadre"',
            'id="tab-ict-cadre"',
            'id="tab-visa-cadre"',
            'id="tab-passport-meta"',
            'id="tab-migration-staff"',
            'id="tab-works-logistics-cadre"',
            'id="tab-border-personnel"',
            'id="investigation-section-1"',
            'name="local_revenue_passport"',
        ] as $marker) {
            $response->assertSee($marker, false);
        }

        // The state layout owns consent + attachments; the embedded investigation
        // declaration must be suppressed (exactly one required consent checkbox).
        $this->assertSame(1, substr_count($response->getContent(), '<input type="checkbox" name="data_consent"'));
        $response->assertSee('name="attachments[]"', false);
        $response->assertSee('name="command_name"', false);
    }

    public function test_combined_form_submission_stores_namespaced_return_data(): void
    {
        $this->disableAbac();

        $user = $this->stateUser();

        $response = $this->actingAs($user)->post(route('user.returns.store'), [
            'command_name' => 'Lagos',
            'period' => '2026-09',
            'return_type' => 'monthly',
            'reporting_officer' => 'Test Officer',
            'data_consent' => '1',
            'hrm' => ['general_report' => ['challenges' => 'HRM challenge text']],
            'passport' => ['staff_strength' => [['male' => 5, 'female' => 3, 'total' => 8]]],
            'works' => ['staff_strength' => [['cadre' => 'comptroller', 'male' => 2, 'female' => 1, 'total' => 3]]],
            'border' => ['general' => ['security' => 'Border security report']],
            'local_revenue_passport' => '1500',
        ]);

        $response->assertRedirect('/user/submissions');

        $application = Application::query()->where('user_id', $user->id)->latest('id')->first();

        $this->assertNotNull($application);
        $this->assertSame('state', $application->category);
        $this->assertSame('LA', $application->scope_code);
        $this->assertSame('desk_review', $application->workflow_stage);
        $this->assertSame('pending', $application->status);
        $this->assertSame('HRM challenge text', $application->return_data['hrm']['general_report']['challenges'] ?? null);
        $this->assertSame(8, $application->return_data['passport']['staff_strength'][0]['total'] ?? null);
        $this->assertSame(3, $application->return_data['works']['staff_strength'][0]['total'] ?? null);
        $this->assertSame('Border security report', $application->return_data['border']['general']['security'] ?? null);
        $this->assertSame(1500, $application->return_data['local_revenue_passport'] ?? null);
    }

    public function test_state_user_cannot_submit_duplicate_return_for_same_period(): void
    {
        $this->disableAbac();

        $user = $this->stateUser();

        $this->actingAs($user)->post(route('user.returns.store'), [
            'command_name' => 'Lagos',
            'period' => '2026-09',
            'return_type' => 'monthly',
            'reporting_officer' => 'Test Officer',
            'data_consent' => '1',
        ])->assertRedirect('/user/submissions');

        $this->assertSame(1, Application::count());

        $response = $this->actingAs($user)->post(route('user.returns.store'), [
            'command_name' => 'Lagos',
            'period' => '2026-09',
            'return_type' => 'monthly',
            'reporting_officer' => 'Test Officer',
            'data_consent' => '1',
        ]);

        $response->assertSessionHasErrors(['period']);
        $this->assertSame(1, Application::count());
    }

    public function test_preview_page_renders_entered_values_without_persisting(): void
    {
        $this->disableAbac();

        $user = $this->stateUser();

        $response = $this->actingAs($user)->post(route('user.returns.preview'), [
            'command_name' => 'Lagos',
            'period' => '2026-09',
            'return_type' => 'monthly',
            'reporting_officer' => 'Preview Officer',
            'data_consent' => '1',
            'hrm' => ['general_report' => ['challenges' => 'Unique preview marker text']],
        ]);

        $response->assertOk();
        $response->assertSee('Preview Officer');
        $response->assertSee('Unique preview marker text');

        $this->assertSame(0, Application::query()->where('user_id', $user->id)->count());
    }

    public function test_edit_form_prefills_own_pending_submission(): void
    {
        $this->disableAbac();

        $user = $this->stateUser();
        $application = SubmissionWorkflow::create($user, [
            'command_name' => 'Lagos',
            'period' => '2026-09',
            'reporting_officer' => 'Edit Officer',
            'hrm' => ['general_report' => ['challenges' => 'Prefill marker']],
        ]);

        $response = $this->actingAs($user)->get($this->applicationRoute('user.returns.edit', $application));

        $response->assertOk();
        $response->assertSee('REDAS_PREFILL', false);
        $response->assertSee('Prefill marker');

        $otherUser = $this->stateUser();
        $this->actingAs($otherUser)->get($this->applicationRoute('user.returns.edit', $application))->assertForbidden();
    }
}
