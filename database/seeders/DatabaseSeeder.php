<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's core data set.
     *
     * This ensures the base admin users exist before the app-specific seeders are called,
     * and it is safe to run repeatedly because each record is checked before being created.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin Test',
                'service_number' => 'NIS/ADM/000',
                'email' => 'admintest@nis.gov.ng',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'user_category' => 'admin',
                'primary_location_type' => 'headquarters',
                'primary_location_code' => 'HQ',
                'geo_state' => 'FC',
                'access_level' => 5,
                'is_enabled' => true,
            ],
            // [
            //     'name' => 'Super Admin User',
            //     'service_number' => 'NIS/SA/001',
            //     'email' => 'superadmin@nis.gov.ng',
            //     'password' => Hash::make('password123'),
            //     'role' => 'admin',
            //     'user_category' => 'super_admin',
            //     'primary_location_type' => 'headquarters',
            //     'primary_location_code' => 'HQ',
            //     'geo_state' => 'FC',
            //     'access_level' => 6,
            //     'is_enabled' => true,
            // ],
            // [
            //     'name' => 'Zonal Commander North',
            //     'service_number' => 'NIS/ZN/001',
            //     'email' => 'zonal.north@nis.gov.ng',
            //     'password' => Hash::make('password123'),
            //     'role' => 'zonal',
            //     'user_category' => 'zonal_commander',
            //     'primary_location_type' => 'zonal',
            //     'primary_location_code' => 'ZONE-A',
            //     'geo_state' => 'KD',
            //     'access_level' => 4,
            //     'is_enabled' => true,
            // ],
            // [
            //     'name' => 'Zonal Commander South',
            //     'service_number' => 'NIS/ZN/002',
            //     'email' => 'zonal.south@nis.gov.ng',
            //     'password' => Hash::make('password123'),
            //     'role' => 'zonal',
            //     'user_category' => 'zonal_commander',
            //     'primary_location_type' => 'zonal',
            //     'primary_location_code' => 'ZONE-B',
            //     'geo_state' => 'LA',
            //     'access_level' => 4,
            //     'is_enabled' => true,
            // ],
            // [
            //     'name' => 'State Coordinator',
            //     'service_number' => 'NIS/ST/001',
            //     'email' => 'state.lagos@nis.gov.ng',
            //     'password' => Hash::make('password123'),
            //     'role' => 'state',
            //     'user_category' => 'desk_admin',
            //     'primary_location_type' => 'state',
            //     'primary_location_code' => 'LA',
            //     'geo_state' => 'LA',
            //     'access_level' => 1,
            //     'is_enabled' => true,
            // ],
            // [
            //     'name' => 'HQ State Coordinator',
            //     'service_number' => 'NIS/ST/002',
            //     'email' => 'state.abuja@nis.gov.ng',
            //     'password' => Hash::make('password123'),
            //     'role' => 'state',
            //     'user_category' => 'desk_admin',
            //     'primary_location_type' => 'state',
            //     'primary_location_code' => 'FC',
            //     'geo_state' => 'FC',
            //     'access_level' => 1,
            //     'is_enabled' => true,
            // ],
            // [
            //     'name' => 'State Desk Officer',
            //     'service_number' => 'NIS/OF/001',
            //     'email' => 'officer1@nis.gov.ng',
            //     'password' => Hash::make('password123'),
            //     'role' => 'officer',
            //     'user_category' => 'state_user',
            //     'primary_location_type' => 'state',
            //     'primary_location_code' => 'KN',
            //     'geo_state' => 'KN',
            //     'access_level' => 0,
            //     'is_enabled' => true,
            // ],
            // [
            //     'name' => 'Directorate Desk Officer',
            //     'service_number' => 'NIS/OF/002',
            //     'email' => 'officer2@nis.gov.ng',
            //     'password' => Hash::make('password123'),
            //     'role' => 'directorate',
            //     'user_category' => 'directorate_user',
            //     'primary_location_type' => 'directorate',
            //     'primary_location_code' => 'HRM',
            //     'geo_state' => 'FC',
            //     'access_level' => 2,
            //     'is_enabled' => true,
            // ],
            // [
            //     'name' => 'Test User',
            //     'service_number' => 'NIS/OF/999',
            //     'email' => 'test@example.com',
            //     'password' => Hash::make('password'),
            //     'role' => 'officer',
            //     'user_category' => 'state_user',
            //     'primary_location_type' => 'state',
            //     'primary_location_code' => 'AB',
            //     'geo_state' => 'AB',
            //     'access_level' => 0,
            //     'is_enabled' => true,
            // ],
        ];

        foreach ($users as $userData) {
            $this->createUser($userData);
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

    private function createUser(array $data): void
    {
        $existingUser = User::query()
            ->where('service_number', $data['service_number'])
            ->orWhere('email', $data['email'])
            ->first();

        $payload = [
            'name' => $data['name'],
            'service_number' => $data['service_number'],
            'email' => $data['email'],
            'role' => $data['role'],
            'user_category' => $data['user_category'],
            'primary_location_type' => $data['primary_location_type'],
            'primary_location_code' => $data['primary_location_code'] ?? null,
            'geo_state' => $data['geo_state'] ?? null,
            'access_level' => $data['access_level'] ?? 0,
            'password' => $data['password'] ?? Hash::make('password123'),
            'email_verified_at' => $data['email_verified_at'] ?? now(),
        ];

        if (Schema::hasColumn('users', 'is_enabled')) {
            $payload['is_enabled'] = $data['is_enabled'] ?? true;
        }

        if ($existingUser) {
            $existingUser->fill($payload);
            $existingUser->save();

            return;
        }

        User::query()->create($payload);
    }
}
