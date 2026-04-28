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
                'service_number' => 'NIS/AD/001',
                'email' => 'admin@nis.gov.ng',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ],
            [
                'name' => 'Zonal Commander North',
                'service_number' => 'NIS/ZN/001',
                'email' => 'zonal.north@nis.gov.ng',
                'password' => Hash::make('password123'),
                'role' => 'zonal',
            ],
            [
                'name' => 'Zonal Commander South',
                'service_number' => 'NIS/ZN/002',
                'email' => 'zonal.south@nis.gov.ng',
                'password' => Hash::make('password123'),
                'role' => 'zonal',
            ],
            [
                'name' => 'State Coordinator',
                'service_number' => 'NIS/ST/001',
                'email' => 'state.lagos@nis.gov.ng',
                'password' => Hash::make('password123'),
                'role' => 'state',
            ],
            [
                'name' => 'HQ State Coordinator',
                'service_number' => 'NIS/ST/002',
                'email' => 'state.abuja@nis.gov.ng',
                'password' => Hash::make('password123'),
                'role' => 'state',
            ],
            [
                'name' => 'State Desk Officer',
                'service_number' => 'NIS/OF/001',
                'email' => 'officer1@nis.gov.ng',
                'password' => Hash::make('password123'),
                'role' => 'officer',
            ],
            [
                'name' => 'Directorate Desk Officer',
                'service_number' => 'NIS/OF/002',
                'email' => 'officer2@nis.gov.ng',
                'password' => Hash::make('password123'),
                'role' => 'directorate',
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        // Also create a test user for general testing
        User::create([
            'name' => 'Test User',
            'service_number' => 'NIS/OF/999',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'officer',
        ]);
    }
}
