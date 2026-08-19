<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class IctReportTest extends TestCase
{
    use RefreshDatabase;

    private function createIctUser(): User
    {
        return User::create([
            'name' => 'ICT Officer Test',
            'service_number' => '77777',
            'email' => 'ict-officer@example.com',
            'password' => Hash::make('Password123!'),
            'role' => 'directorate',
            'user_category' => 'directorate_user',
            'primary_location_type' => 'directorate',
            'primary_location_code' => 'ICT',
            'access_level' => 2,
        ]);
    }

    private function createIctSupervisor(): User
    {
        return User::create([
            'name' => 'ICT Supervisor Test',
            'service_number' => '88888',
            'email' => 'ict-supervisor@example.com',
            'password' => Hash::make('Password123!'),
            'role' => 'directorate',
            'user_category' => 'directorate_admin',
            'primary_location_type' => 'directorate',
            'primary_location_code' => 'ICT',
            'access_level' => 3,
        ]);
    }

    public function test_ict_user_can_access_dashboard_and_form(): void
    {
        $user = $this->createIctUser();

        // Dashboard redirects to submissions list for desk officer
        $response = $this->actingAs($user)->get('/user/directorate/ict');
        $response->assertRedirect('/user/directorate/ict/submissions');

        $response = $this->actingAs($user)->get('/user/directorate/ict/report');
        $response->assertOk();
        $response->assertSee('Annual ICT &amp; Cybersecurity Report', false);

        // Reports redirects to workspace for desk officer
        $response = $this->actingAs($user)->get('/user/directorate/ict/reports');
        $response->assertRedirect('/user/directorate/ict/report');

        $response = $this->actingAs($user)->get('/user/directorate/ict/submissions');
        $response->assertOk();
        $response->assertSee('Submitted Reports');
    }

    public function test_ict_user_can_save_draft_report(): void
    {
        $user = $this->createIctUser();

        $payload = [
            'action' => 'draft',
            'report_year' => '2026',
            'remarks' => 'ICT draft test remarks',
            'staff' => [
                'comptroller' => ['male' => 5, 'female' => 2],
            ],
            'projects' => [
                ['project_name' => 'Portal Upgrade', 'vendor' => 'Cyber Ltd', 'status' => 'Ongoing'],
            ]
        ];

        $response = $this->actingAs($user)->post('/user/directorate/ict/report', $payload);

        $response->assertRedirect('/user/directorate/ict/report?year=2026');
        $response->assertSessionHas('status', 'ICT & Cybersecurity Annual Report draft saved successfully.');

        $this->assertDatabaseHas('applications', [
            'user_id' => $user->id,
            'type' => 'ict_cybersecurity',
            'period' => '2026',
            'status' => 'draft',
        ]);
    }

    public function test_ict_user_can_submit_annual_report(): void
    {
        $user = $this->createIctUser();

        $payload = [
            'action' => 'submit',
            'report_year' => '2026',
            'remarks' => 'Submitted ICT report',
            'staff' => [
                'comptroller' => ['male' => 5, 'female' => 2],
            ],
            'projects' => [
                ['project_name' => 'Portal Upgrade', 'vendor' => 'Cyber Ltd', 'status' => 'Ongoing'],
            ]
        ];

        $response = $this->actingAs($user)->post('/user/directorate/ict/report', $payload);

        $response->assertRedirect('/user/directorate/ict/report?year=2025');
        $response->assertSessionHas('status', 'Your report has been sent for approval and you will be notified when approval is given.');

        $this->assertDatabaseHas('applications', [
            'user_id' => $user->id,
            'type' => 'ict_cybersecurity',
            'period' => '2026',
            'status' => 'pending',
            'comments' => 'Submitted ICT report',
        ]);
    }

    public function test_ict_dashboard_displays_dynamic_stats_from_database(): void
    {
        $user = $this->createIctUser();
        $supervisor = $this->createIctSupervisor();

        Application::create([
            'user_id' => $supervisor->id,
            'type' => 'ict_cybersecurity',
            'period' => '2026',
            'status' => 'pending',
            'return_data' => [
                'staff' => [
                    'comptroller' => ['male' => 3, 'female' => 2]
                ],
                'projects' => [
                    ['project_name' => 'Update 1'],
                    ['project_name' => 'Update 2']
                ],
                'maintenance' => [
                    ['equipment_type' => 'Server', 'product' => 'HP DL380', 'status' => 'Serviceable', 'location' => 'HQ Data Center']
                ],
                'midas' => [
                    ['state_command' => 'Kano']
                ]
            ]
        ]);

        $response = $this->actingAs($supervisor)->get('/user/directorate/ict');
        $response->assertOk();

        // 3 + 2 = 5 Staff Strength
        $response->assertSee('5');
        // 2 Projects
        $response->assertSee('2');
        // 1 Maintenance case
        $response->assertSee('1');
    }
}
