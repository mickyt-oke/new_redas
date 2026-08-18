<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DirectorateSubmissionTest extends TestCase
{
    use RefreshDatabase;

    private function directorateUser(string $code = 'HRM'): User
    {
        return User::factory()->create([
            'role' => 'directorate',
            'user_category' => 'directorate_user',
            'primary_location_type' => 'directorate',
            'primary_location_code' => $code,
            'assigned_directorate_code' => $code,
            'access_level' => 0,
        ]);
    }

    public function test_directorate_user_can_submit_return_and_it_is_stored_in_the_database(): void
    {
        Storage::fake(config('filesystems.default'));

        $user = $this->directorateUser('HRM');

        $response = $this->actingAs($user)->post(route('user.directorates.store', ['slug' => 'hrm']), [
            'report_period' => '2026-08',
            'reporting_officer' => $user->name,
            'data_consent' => '1',
            'hrm' => ['nimcos' => ['total_membership' => '5']],
            'general_report' => ['challenges' => 'None'],
            'supporting_documents' => [
                UploadedFile::fake()->create('minutes.pdf', 100, 'application/pdf'),
            ],
        ]);

        $response->assertRedirect(route('user.directorates.show', ['slug' => 'hrm']));
        $response->assertSessionHasNoErrors();

        $application = Application::query()->sole();

        $this->assertSame($user->id, $application->user_id);
        $this->assertSame('pending', $application->status);
        $this->assertSame('directorate_review', $application->workflow_stage);
        $this->assertSame('directorate', $application->category);
        $this->assertSame('hrm', $application->scope_code);
        $this->assertSame('2026-08', $application->return_data['report_period']);
        $this->assertSame('hrm', $application->return_data['directorate_slug']);
        $this->assertSame('5', $application->return_data['hrm']['nimcos']['total_membership']);
        $this->assertSame('None', $application->return_data['general_report']['challenges']);

        $stored = $application->return_data['supporting_documents'];
        $this->assertCount(1, $stored);
        Storage::disk(config('filesystems.default'))->assertExists($stored[0]);
    }

    public function test_submission_is_rejected_without_consent_and_report_period(): void
    {
        $user = $this->directorateUser('PRS');

        $response = $this->actingAs($user)->post(route('user.directorates.store', ['slug' => 'prs']), [
            'reporting_officer' => $user->name,
        ]);

        $response->assertSessionHasErrors(['report_period', 'data_consent']);
        $this->assertSame(0, Application::count());
    }

    public function test_directorate_user_cannot_submit_another_directorates_return(): void
    {
        $user = $this->directorateUser('PRS');

        $response = $this->actingAs($user)->post(route('user.directorates.store', ['slug' => 'hrm']), [
            'report_period' => '2026-08',
            'reporting_officer' => $user->name,
            'data_consent' => '1',
        ]);

        $response->assertForbidden();
        $this->assertSame(0, Application::count());
    }

    public function test_all_directorate_forms_render_successfully(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'user_category' => 'directorate_admin',
            'primary_location_type' => 'directorate',
            'primary_location_code' => 'HRM',
            'assigned_directorate_code' => 'HRM',
            'access_level' => 3,
        ]);

        foreach (['hrm', 'prs', 'finance', 'investigation', 'passport', 'visa', 'migration', 'border', 'ict', 'works-logistics'] as $slug) {
            $response = $this->actingAs($user)->get(route('user.directorates.show', ['slug' => $slug]));

            $response->assertOk()->assertViewIs('user.directorates.' . $slug);
        }
    }
}
