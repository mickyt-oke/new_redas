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
            ['first_name' => 'SA', 'last_name' => 'OBAJE', 'email' => 'obajeameh@gmail.com'],
            ['first_name' => 'YA', 'last_name' => 'NASIRU', 'email' => 'nasyak14@gmail.com'],
            ['first_name' => 'MD', 'last_name' => 'ABDULWAHAB', 'email' => 'quaresmary3@gmail.com'],
            ['first_name' => 'E', 'last_name' => 'EKHATOR', 'email' => 'edefe.ekhator@gmail.com'],
            ['first_name' => 'BB', 'last_name' => 'MFONOBONG', 'email' => 'bmfonobong@gmail.com'],
            ['first_name' => 'A', 'last_name' => 'HUSSEIN', 'email' => 'adamshusen@gmail.com'],
            ['first_name' => 'SM', 'last_name' => 'ZANNA', 'email' => 'smzanna@gmail.com'],
            ['first_name' => 'MM', 'last_name' => 'AKEJU', 'email' => 'rosemarymichaelakeju@gmail.com'],
            ['first_name' => 'AM', 'last_name' => 'FRIDAY', 'email' => 'standbic5@gmail.com'],
            ['first_name' => 'GO', 'last_name' => 'ADA', 'email' => 'gabrielochoche014@gmail.com'],
            ['first_name' => 'AA', 'last_name' => 'OHANUGO', 'email' => 'augustineohanugo@gmail.com'],
            ['first_name' => 'GW', 'last_name' => 'DAGOGO', 'email' => 'dago26go@gmail.com'],
            ['first_name' => 'MO', 'last_name' => 'OKE', 'email' => 'okemichael@yahoo.com'],
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
                    'email' => $officer['email'],
                    'password' => $password,
                    'role' => 'directorate',
                    'user_category' => 'directorate_user',
                    'primary_location_type' => 'directorate',
                    'primary_location_code' => $directorate['code'],
                    'geo_state' => 'FC',
                    'access_level' => 1,
                    'assigned_directorate_code' => $directorate['code'],
                ]);
            }
        }
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
