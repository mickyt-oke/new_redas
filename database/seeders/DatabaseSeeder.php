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
        $now = now();

        $users = [
            [
                'name' => 'Admin User',
                'service_number' => 'NIS/AD/001',
                'email' => 'admin@nis.gov.ng',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'user_category' => 'admin',
                'primary_location_type' => 'headquarters',
                'primary_location_code' => 'HQ',
                'geo_state' => 'FC',
                'access_level' => 5,
            ],
            [
                'name' => 'Super Admin User',
                'service_number' => 'NIS/SA/001',
                'email' => 'superadmin@nis.gov.ng',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'user_category' => 'super_admin',
                'primary_location_type' => 'headquarters',
                'primary_location_code' => 'HQ',
                'geo_state' => 'FC',
                'access_level' => 6,
            ],
            [
                'name' => 'Zonal Commander North',
                'service_number' => 'NIS/ZN/001',
                'email' => 'zonal.north@nis.gov.ng',
                'password' => Hash::make('password123'),
                'role' => 'zonal',
                'user_category' => 'zonal_commander',
                'primary_location_type' => 'zonal',
                'primary_location_code' => 'ZONE-A',
                'geo_state' => 'KD',
                'access_level' => 4,
            ],
            [
                'name' => 'Zonal Commander South',
                'service_number' => 'NIS/ZN/002',
                'email' => 'zonal.south@nis.gov.ng',
                'password' => Hash::make('password123'),
                'role' => 'zonal',
                'user_category' => 'zonal_commander',
                'primary_location_type' => 'zonal',
                'primary_location_code' => 'ZONE-B',
                'geo_state' => 'LA',
                'access_level' => 4,
            ],
            [
                'name' => 'State Coordinator',
                'service_number' => 'NIS/ST/001',
                'email' => 'state.lagos@nis.gov.ng',
                'password' => Hash::make('password123'),
                'role' => 'state',
                'user_category' => 'desk_admin',
                'primary_location_type' => 'state',
                'primary_location_code' => 'LA',
                'geo_state' => 'LA',
                'access_level' => 1,
            ],
            [
                'name' => 'HQ State Coordinator',
                'service_number' => 'NIS/ST/002',
                'email' => 'state.abuja@nis.gov.ng',
                'password' => Hash::make('password123'),
                'role' => 'state',
                'user_category' => 'desk_admin',
                'primary_location_type' => 'state',
                'primary_location_code' => 'FC',
                'geo_state' => 'FC',
                'access_level' => 1,
            ],
            [
                'name' => 'State Desk Officer',
                'service_number' => 'NIS/OF/001',
                'email' => 'officer1@nis.gov.ng',
                'password' => Hash::make('password123'),
                'role' => 'officer',
                'user_category' => 'state_user',
                'primary_location_type' => 'state',
                'primary_location_code' => 'KN',
                'geo_state' => 'KN',
                'access_level' => 0,
            ],
            [
                'name' => 'Directorate Desk Officer',
                'service_number' => 'NIS/OF/002',
                'email' => 'officer2@nis.gov.ng',
                'password' => Hash::make('password123'),
                'role' => 'directorate',
                'user_category' => 'directorate_user',
                'primary_location_type' => 'directorate',
                'primary_location_code' => 'HRM',
                'geo_state' => 'FC',
                'access_level' => 2,
            ],
            [
                'name' => 'Test User',
                'service_number' => 'NIS/OF/999',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'role' => 'officer',
                'user_category' => 'state_user',
                'primary_location_type' => 'state',
                'primary_location_code' => 'AB',
                'geo_state' => 'AB',
                'access_level' => 0,
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData + ['email_verified_at' => $now]);
        }

        $this->call([
            NewSeeder::class,
            EmailTemplateSeeder::class,
            PrimaryLocationTypeSeeder::class,
            PrimaryLocationCodeSeeder::class,
            SettingSeeder::class,
            NisDirectorySeeder::class,
        ]);
    }
}
