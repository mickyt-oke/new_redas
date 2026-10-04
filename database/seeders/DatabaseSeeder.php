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
        // Zone User
        User::firstOrCreate(
            ['email' => 'zone.user@nis.gov.ng'],
            [
                'name' => 'Zone User',
                'service_number' => 'NIS/ZN/001',
                'user_category' => 'zonal_user',
                'role' => 'user',
                'primary_location_type' => 'zonal',
                'primary_location_code' => 'ZONE_A',
                'password' => Hash::make('password'),
                'is_enabled' => true,
                'access_level' => 1,
            ]
        );

        // Zonal Admin (Commander)
        User::firstOrCreate(
            ['email' => 'zone.admin@nis.gov.ng'],
            [
                'name' => 'Zonal Admin',
                'service_number' => 'NIS/ZN/1000',
                'user_category' => 'zonal_commander',
                'role' => 'admin',
                'primary_location_type' => 'zonal',
                'primary_location_code' => 'ZONE_A',
                'password' => Hash::make('password'),
                'is_enabled' => true,
                'access_level' => 2,
            ]
        );

        // Special Command User
        User::firstOrCreate(
            ['email' => 'special.user@nis.gov.ng'],
            [
                'name' => 'Special Command User',
                'service_number' => 'NIS/SC/1001',
                'user_category' => 'state_user',
                'role' => 'user',
                'primary_location_type' => 'state',
                'primary_location_code' => 'SEME',
                'password' => Hash::make('password'),
                'is_enabled' => true,
                'access_level' => 1,
            ]
        );
    }



}
