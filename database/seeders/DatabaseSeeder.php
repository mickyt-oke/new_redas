<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create mock users with different roles for NIS
        $users = [
            [
                'name' => 'Admin User',
                'service_number' => '10001',
                'email' => 'admin@nis.gov.ng',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'user_category' => 'admin',
                'primary_location_type' => 'headquarters',
                'primary_location_code' => 'HQ',
                'access_level' => 5,
            ],
            [
                'name' => 'Zonal Commander North',
                'service_number' => '10002',
                'email' => 'zonal.north@nis.gov.ng',
                'password' => Hash::make('password123'),
                'role' => 'zonal',
                'user_category' => 'zonal_commander',
                'primary_location_type' => 'zonal',
                'primary_location_code' => 'ZONE_A',
                'access_level' => 4,
            ],
            [
                'name' => 'Zonal Commander South',
                'service_number' => '10003',
                'email' => 'zonal.south@nis.gov.ng',
                'password' => Hash::make('password123'),
                'role' => 'zonal',
                'user_category' => 'zonal_commander',
                'primary_location_type' => 'zonal',
                'primary_location_code' => 'ZONE_B',
                'access_level' => 4,
            ],
            [
                'name' => 'State Coordinator',
                'service_number' => '10004',
                'email' => 'state.lagos@nis.gov.ng',
                'password' => Hash::make('password123'),
                'role' => 'state',
                'user_category' => 'desk_admin',
                'primary_location_type' => 'state',
                'primary_location_code' => 'LAGOS',
                'access_level' => 1,
            ],
            [
                'name' => 'HQ State Coordinator',
                'service_number' => '10005',
                'email' => 'state.abuja@nis.gov.ng',
                'password' => Hash::make('password123'),
                'role' => 'state',
                'user_category' => 'desk_admin',
                'primary_location_type' => 'state',
                'primary_location_code' => 'ABUJA',
                'access_level' => 1,
            ],
            [
                'name' => 'State Desk Officer',
                'service_number' => '10006',
                'email' => 'officer1@nis.gov.ng',
                'password' => Hash::make('password123'),
                'role' => 'officer',
                'user_category' => 'state_user',
                'primary_location_type' => 'state',
                'primary_location_code' => 'LAGOS',
                'access_level' => 0,
            ],
            [
                'name' => 'Directorate Desk Officer',
                'service_number' => '10007',
                'email' => 'officer2@nis.gov.ng',
                'password' => Hash::make('password123'),
                'role' => 'directorate',
                'user_category' => 'directorate_user',
                'primary_location_type' => 'directorate',
                'primary_location_code' => 'VISA',
                'access_level' => 2,
            ],
            [
                'name' => 'ICT Directorate Officer',
                'service_number' => '10008',
                'email' => 'ict-officer@nis.gov.ng',
                'password' => Hash::make('password123'),
                'role' => 'directorate',
                'user_category' => 'directorate_user',
                'primary_location_type' => 'directorate',
                'primary_location_code' => 'ICT',
                'access_level' => 2,
            ],
            [
                'name' => 'Visa Directorate Admin',
                'service_number' => '10009',
                'email' => 'visa-admin@nis.gov.ng',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'user_category' => 'directorate_admin',
                'primary_location_type' => 'directorate',
                'primary_location_code' => 'VISA',
                'access_level' => 3,
            ],
            [
                'name' => 'ICT Directorate Admin',
                'service_number' => '10010',
                'email' => 'ict-admin@nis.gov.ng',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'user_category' => 'directorate_admin',
                'primary_location_type' => 'directorate',
                'primary_location_code' => 'ICT',
                'access_level' => 3,
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        // Also create a test user for general testing
        User::create([
            'name' => 'Test User',
            'service_number' => '99999',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'officer',
        ]);

        $this->call([
            EmailTemplateSeeder::class,
            PrimaryLocationTypeSeeder::class,
            PrimaryLocationCodeSeeder::class,
        ]);
    }
}
