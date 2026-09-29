<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
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
            ['first_name' => '', 'last_name' => 'Oke', 'email' => ''],
            ['first_name' => '', 'last_name' => 'Akintunde', 'email' => ''],
            ['first_name' => '', 'last_name' => 'Dagogo', 'email' => ''],
            ['first_name' => '', 'last_name' => 'Eze', 'email' => ''],
            ['first_name' => '', 'last_name' => 'Taiwo', 'email' => ''],
            ['first_name' => '', 'last_name' => 'Baruwa', 'email' => ''],
            ['first_name' => '', 'last_name' => 'Kawu', 'email' => ''],
            ['first_name' => '', 'last_name' => 'Adewumi', 'email' => ''],
            ['first_name' => '', 'last_name' => 'Oshifekun', 'email' => ''],
            ['first_name' => '', 'last_name' => 'Ige', 'email' => ''],
            ['first_name' => '', 'last_name' => 'Ogheneovo', 'email' => ''],
            ['first_name' => '', 'last_name' => 'Akinsowon', 'email' => '']
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

        // Create directorate_user for each officer. Each officer has login for all directorates with access level 1
        foreach ($officers as $index => $officer) {
            foreach ($directorates as $directorate) {
                $this->createOrUpdateUser([
                    'name' => $officer['last_name'].' '.$officer['first_name'].' '.$directorate['code'].' User',
                    'service_number' => 'NIS/'.$directorate['code'].'/'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                    'email' => strtolower($officer['last_name'].'.'.$directorate['code'].'@nis.gov.ng'),
                    'password' => $password,
                    'role' => 'directorate',
                    'user_category' => 'directorate_user',
                    'primary_location_type' => 'directorate',
                    'primary_location_code' => $directorate['code'],
                    'geo_state' => 'FC',
                    'access_level' => 1,
                    'assigned_directorate_code' => $directorate['code'],
                    'email_verified_at' => $now,
                ]);
            }
        }

        // Create a super admin user with access level 3

        // Create directorate_admin user for each officers. Each officer has login for all directorates with access level 2
        // foreach ($officers as $index => $officer) {
        //     foreach ($directorates as $directorate) {
        //         $this->createOrUpdateUser([
        //             'name' => $officer['last_name'].' '.$officer['first_name'].' '.$directorate['code'].' Admin',
        //             'service_number' => 'NIS/'.$directorate['code'].'/'.str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT),
        //             'email' => strtolower($officer['last_name'].'.'.$officer['first_name'].'.'.$directorate['code'].'@nis.gov.ng'),
        //             'password' => $password,
        //             'role' => 'admin',
        //             'user_category' => 'directorate_admin',
        //             'primary_location_type' => 'directorate',
        //             'primary_location_code' => $directorate['code'],
        //             'geo_state' => 'FC',
        //             'access_level' => 2,
        //             'assigned_directorate_code' => $directorate['code'],
        //             'email_verified_at' => $now,
        //         ]);
        //     }
        // }
    }

    // create user if not exists, otherwise update the existing user with the new data
    private function createOrUpdateUser(array $userData): void
    {
        $user = User::query()->where('email', $userData['email'])->first();

        if ($user) {
            // Update existing user
            $user->update($userData);
            return;
        }

        User::create($userData);
    }
}
