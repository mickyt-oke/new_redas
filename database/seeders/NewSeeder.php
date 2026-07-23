<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class NewSeeder extends Seeder
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
            
        ];

        foreach ($users as $userData) {
            User::create($userData + ['email_verified_at' => $now]);
        }
    }
}
