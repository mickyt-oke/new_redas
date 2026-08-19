<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VisaReportTest extends TestCase
{
    use RefreshDatabase;

    private function createVisaUser(): User
    {
        return User::create([
            'name' => 'Visa Officer Test',
            'service_number' => '77777',
            'email' => 'visa-officer@example.com',
            'password' => Hash::make('Password123!'),
            'role' => 'directorate',
            'user_category' => 'directorate_user',
            'primary_location_type' => 'directorate',
            'primary_location_code' => 'VISA',
            'access_level' => 2,
        ]);
    }

    private function createVisaSupervisor(): User
    {
        return User::create([
            'name' => 'Visa Supervisor Test',
            'service_number' => '88888',
            'email' => 'visa-supervisor@example.com',
            'password' => Hash::make('Password123!'),
            'role' => 'directorate',
            'user_category' => 'directorate_admin',
            'primary_location_type' => 'directorate',
            'primary_location_code' => 'VISA',
            'access_level' => 3,
        ]);
    }

    public function test_visa_user_can_access_dashboard_and_form(): void
    {
        $user = $this->createVisaUser();

        // Dashboard redirects to submissions list for desk officer
        $response = $this->actingAs($user)->get('/user/directorate/visa');
        $response->assertRedirect('/user/directorate/visa/submissions');

        $response = $this->actingAs($user)->get('/user/directorate/visa/report');
        $response->assertOk();
        $response->assertSee('Annual Visa & Residence Report', false);

        // Reports redirects to workspace for desk officer
        $response = $this->actingAs($user)->get('/user/directorate/visa/reports');
        $response->assertRedirect('/user/directorate/visa/report');

        $response = $this->actingAs($user)->get('/user/directorate/visa/submissions');
        $response->assertOk();
        $response->assertSee('Submitted Reports');
    }

    public function test_visa_user_can_save_draft_report(): void
    {
        $user = $this->createVisaUser();

        $payload = [
            'action' => 'draft',
            'report_year' => '2026',
            'remarks' => 'Draft test remarks',
            'staff' => [
                'comptroller' => ['male' => 2, 'female' => 3],
            ],
            'quota' => [
                ['company' => 'A Company', 'positions' => 5, 'industry' => 'Tech'],
            ]
        ];

        $response = $this->actingAs($user)->post('/user/directorate/visa/report', $payload);

        $response->assertRedirect('/user/directorate/visa/report?year=2026');
        $response->assertSessionHas('status', 'Visa & Residence Annual Report draft saved successfully.');

        $this->assertDatabaseHas('applications', [
            'user_id' => $user->id,
            'type' => 'visa',
            'period' => '2026',
            'status' => 'draft',
        ]);
    }

    public function test_visa_user_can_submit_annual_report(): void
    {
        $user = $this->createVisaUser();

        $payload = [
            'action' => 'submit',
            'report_year' => '2026',
            'remarks' => 'Submitted operational report',
            'staff' => [
                'comptroller' => ['male' => 2, 'female' => 3],
            ],
            'quota' => [
                ['company' => 'A Company', 'positions' => 5, 'industry' => 'Tech'],
            ]
        ];

        $response = $this->actingAs($user)->post('/user/directorate/visa/report', $payload);

        $response->assertRedirect('/user/directorate/visa/report?year=2025');
        $response->assertSessionHas('status', 'Your report has been sent for approval and you will be notified when approval is given.');

        $this->assertDatabaseHas('applications', [
            'user_id' => $user->id,
            'type' => 'visa',
            'period' => '2026',
            'status' => 'pending',
            'comments' => 'Submitted operational report',
        ]);
    }

    public function test_dashboard_displays_dynamic_stats_from_database(): void
    {
        $user = $this->createVisaUser();
        $supervisor = $this->createVisaSupervisor();

        Application::create([
            'user_id' => $supervisor->id,
            'type' => 'visa',
            'period' => '2026',
            'status' => 'pending',
            'return_data' => [
                'residence_temporary' => [
                    'Diplomat (Accredited)' => ['male' => 2, 'female' => 3, 'principal' => 5],
                ],
                'visa_applications' => [
                    'F3B' => ['applications' => 25],
                ],
                'cerpac' => [
                    'issued' => 15,
                ],
                'ftz' => [
                    'enterprises' => 8,
                ]
            ]
        ]);

        $response = $this->actingAs($supervisor)->get('/user/directorate/visa');
        $response->assertOk();

        // 2 + 3 + 5 = 10 Residence Permits
        $response->assertSee('10');
        // 25 Visa Applications
        $response->assertSee('25');
        // 15 CERPAC Issued
        $response->assertSee('15');
        // 8 FTZ Enterprises
        $response->assertSee('8');
    }
}
