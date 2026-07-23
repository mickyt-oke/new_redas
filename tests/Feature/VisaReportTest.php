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
            'service_number' => 'NIS/VIS/7777',
            'email' => 'visa-officer@example.com',
            'password' => Hash::make('Password123!'),
            'role' => 'directorate',
            'user_category' => 'directorate_user',
            'primary_location_type' => 'directorate',
            'primary_location_code' => 'VISA',
            'access_level' => 2,
        ]);
    }

    public function test_visa_user_can_access_dashboard_and_form(): void
    {
        $user = $this->createVisaUser();

        $response = $this->actingAs($user)->get('/user/directorate/visa');
        $response->assertOk();
        $response->assertSee('Visa & Residence Annual Reporting Dashboard', false);

        $response = $this->actingAs($user)->get('/user/directorate/visa/report');
        $response->assertOk();
        $response->assertSee('Annual Visa & Residence Report', false);

        $response = $this->actingAs($user)->get('/user/directorate/visa/reports');
        $response->assertOk();
        $response->assertSee('Visa &amp; Residence Analytics &amp; Reports', false);

        $response = $this->actingAs($user)->get('/user/directorate/visa/submissions');
        $response->assertOk();
        $response->assertSee('Submitted Returns');
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
                'superintendent' => ['male' => 4, 'female' => 5],
            ],
            'ftz' => [
                'zones' => 1,
                'enterprises' => 10,
                'expatriates' => 15,
            ]
        ];

        $response = $this->actingAs($user)->post('/user/directorate/visa/report', $payload);

        $response->assertRedirect('/user/directorate/visa/submissions');
        $response->assertSessionHas('status', 'Visa & Residence Annual Report draft saved successfully.');

        $this->assertDatabaseHas('applications', [
            'user_id' => $user->id,
            'type' => 'visa',
            'period' => '2026',
            'status' => 'draft',
            'comments' => 'Draft test remarks',
        ]);

        $app = Application::first();
        $this->assertEquals(10, $app->return_data['ftz']['enterprises']);
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
            'cerpac' => [
                'supplied' => 100,
                'produced' => 90,
                'damaged' => 2,
                'issued' => 88,
            ]
        ];

        $response = $this->actingAs($user)->post('/user/directorate/visa/report', $payload);

        $response->assertRedirect('/user/directorate/visa/submissions');
        $response->assertSessionHas('status', 'Visa & Residence Annual Report submitted successfully.');

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

        Application::create([
            'user_id' => $user->id,
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

        $response = $this->actingAs($user)->get('/user/directorate/visa');
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
