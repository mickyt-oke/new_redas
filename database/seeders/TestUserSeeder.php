<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with workflow-aligned 10 users for directorate user and admin category only.
     * create username for all users in the format of firstname.lastname@nis.gov.ng and generic password for all users as 'password123'.
     */
    public function run(): void
    {
        $now = now();
        $password = Hash::make('password123');

        $officers = [
            ['first_name' => 'Michael', 'last_name' => 'Oke', 'email' => 'michael.oke@nis.gov.ng'],
            ['first_name' => 'Shefiu', 'last_name' => 'Akintunde', 'email' => 'shefiu.akintunde@nis.gov.ng'],
            ['first_name' => 'Gift', 'last_name' => 'Dagogo', 'email' => 'gift.dagogo@nis.gov.ng'],
            ['first_name' => 'Adamma', 'last_name' => 'Eze', 'email' => 'adamma.eze@nis.gov.ng'],
            ['first_name' => 'Temitope', 'last_name' => 'Taiwo', 'email' => 'temitope.taiwo@nis.gov.ng'],
            ['first_name' => 'Nifemi', 'last_name' => 'Baruwa', 'email' => 'nifemi.baruwa@nis.gov.ng'],
            ['first_name' => 'Mohammed', 'last_name' => 'Kawu', 'email' => 'mohammed.kawu@nis.gov.ng'],
            ['first_name' => 'Victor', 'last_name' => 'Adewumi', 'email' => 'victor.adewumi@nis.gov.ng'],
            ['first_name' => 'Precious', 'last_name' => 'Oshifekun', 'email' => 'precious.oshifekun@nis.gov.ng'],
            ['first_name' => 'Moses', 'last_name' => 'Ige', 'email' => 'moses.ige@nis.gov.ng'],
        ];


        $directorates = [
            ['code' => 'HRM', 'name' => 'Human Resources Management'],
            ['code' => 'PRS', 'name' => 'Planning, Research and Statistics'],
            ['code' => 'FIN', 'name' => 'Finance and Accounts'],
            ['code' => 'ICT', 'name' => 'Information and Communication Technology'],
            ['code' => 'WKS', 'name' => 'Works and Logistics'],
            ['code' => 'PAS', 'name' => 'Passport and Other Travel Documents'],
            ['code' => 'INV', 'name' => 'Investigation and Compliance'],
            ['code' => 'VIS', 'name' => 'Visa and Residency'],
            ['code' => 'BOR', 'name' => 'Border Management'],
            ['code' => 'MIG', 'name' => 'Migration'],
        ];

        foreach ($directorates as $index => $directorate) {
            $officer = $officers[$index];
            User::create([
                'name' => $officer['first_name'].' '.$officer['last_name'],
                'service_number' => 'NIS/USER/'.str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'email' => $officer['email'],
                'password' => $password,
                'role' => 'directorate',
                'user_category' => 'directorate_user',
                'primary_location_type' => 'directorate',
                'primary_location_code' => $directorate['code'],
                'geo_state' => 'FC',
                'access_level' => 0,
                'assigned_directorate_code' => $directorate['code'],
                'email_verified_at' => $now,
            ]);
        }

        // Create directorate_admin user for all officers
        foreach ($directorates as $index => $directorate) {
            $officer = $officers[$index];
            User::create([
                'name' => $officer['last_name'].' '.$officer['first_name'],
                'service_number' => 'NIS/ADM/'.str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'email' => $officer['last_name'].'.'.$officer['first_name'].'@nis.gov.ng',
                'password' => $password,
                'role' => 'admin',
                'user_category' => 'directorate_admin',
                'primary_location_type' => 'directorate',
                'primary_location_code' => $directorate['code'],
                'geo_state' => 'FC',
                'access_level' => 3,
                'assigned_directorate_code' => $directorate['code'],
                'email_verified_at' => $now,
            ]);
        }

    }

    private function createUser(array $data): void
    {
        $user = User::firstOrNew(['service_number' => $data['service_number']]);
        $user->forceFill($data + [
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $user->save();
    }

    }
