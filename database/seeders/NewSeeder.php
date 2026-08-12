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
     * Seed the application's database with workflow-aligned users for all supported categories.
     */
    public function run(): void
    {
        $now = now();
        $password = Hash::make('password123');

        $states = [
            ['code' => 'AB', 'name' => 'Abia'],
            ['code' => 'AD', 'name' => 'Adamawa'],
            ['code' => 'AK', 'name' => 'Akwa Ibom'],
            ['code' => 'AN', 'name' => 'Anambra'],
            ['code' => 'BA', 'name' => 'Bauchi'],
            ['code' => 'BY', 'name' => 'Bayelsa'],
            ['code' => 'BE', 'name' => 'Benue'],
            ['code' => 'BO', 'name' => 'Borno'],
            ['code' => 'CR', 'name' => 'Cross River'],
            ['code' => 'DE', 'name' => 'Delta'],
            ['code' => 'EB', 'name' => 'Ebonyi'],
            ['code' => 'ED', 'name' => 'Edo'],
            ['code' => 'EK', 'name' => 'Ekiti'],
            ['code' => 'EN', 'name' => 'Enugu'],
            ['code' => 'FC', 'name' => 'FCT'],
            ['code' => 'GO', 'name' => 'Gombe'],
            ['code' => 'IM', 'name' => 'Imo'],
            ['code' => 'JI', 'name' => 'Jigawa'],
            ['code' => 'KD', 'name' => 'Kaduna'],
            ['code' => 'KN', 'name' => 'Kano'],
            ['code' => 'KT', 'name' => 'Katsina'],
            ['code' => 'KE', 'name' => 'Kebbi'],
            ['code' => 'KO', 'name' => 'Kogi'],
            ['code' => 'KW', 'name' => 'Kwara'],
            ['code' => 'LA', 'name' => 'Lagos'],
            ['code' => 'NA', 'name' => 'Nasarawa'],
            ['code' => 'NI', 'name' => 'Niger'],
            ['code' => 'OG', 'name' => 'Ogun'],
            ['code' => 'ON', 'name' => 'Ondo'],
            ['code' => 'OS', 'name' => 'Osun'],
            ['code' => 'OY', 'name' => 'Oyo'],
            ['code' => 'PL', 'name' => 'Plateau'],
            ['code' => 'RI', 'name' => 'Rivers'],
            ['code' => 'SO', 'name' => 'Sokoto'],
            ['code' => 'TA', 'name' => 'Taraba'],
            ['code' => 'YO', 'name' => 'Yobe'],
            ['code' => 'ZA', 'name' => 'Zamfara'],
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

        $zones = [
            ['code' => 'ZONE-A', 'name' => 'Lagos Zonal HQ'],
            ['code' => 'ZONE-B', 'name' => 'Kaduna Zonal HQ'],
            ['code' => 'ZONE-C', 'name' => 'Bauchi Zonal HQ'],
            ['code' => 'ZONE-D', 'name' => 'Minna Zonal HQ'],
            ['code' => 'ZONE-E', 'name' => 'Owerri Zonal HQ'],
            ['code' => 'ZONE-F', 'name' => 'Ibadan Zonal HQ'],
            ['code' => 'ZONE-G', 'name' => 'Benin Zonal HQ'],
            ['code' => 'ZONE-H', 'name' => 'Makurdi Zonal HQ'],
        ];

        foreach ($states as $index => $state) {
            $this->createUser([
                'name' => $state['name'].' State Officer',
                'service_number' => 'NIS/STATE/'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                'email' => strtolower('state.'.$state['code'].'@nis.gov.ng'),
                'password' => $password,
                'role' => 'officer',
                'user_category' => 'state_user',
                'primary_location_type' => 'state',
                'primary_location_code' => $state['code'],
                'geo_state' => $state['code'],
                'access_level' => 0,
                'assigned_state_code' => $state['code'],
                'assigned_desk_admin_code' => $state['code'],
                'email_verified_at' => $now,
            ]);

            $zoneCode = $zones[$index % count($zones)]['code'];

            $this->createUser([
                'name' => $state['name'].' Desk Admin',
                'service_number' => 'NIS/DSK/'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                'email' => strtolower('desk.'.$state['code'].'.seed@nis.gov.ng'),
                'password' => $password,
                'role' => 'state',
                'user_category' => 'desk_admin',
                'primary_location_type' => 'state',
                'primary_location_code' => $state['code'],
                'geo_state' => $state['code'],
                'access_level' => 1,
                'assigned_state_code' => $state['code'],
                'assigned_zonal_command_code' => $zoneCode,
                'email_verified_at' => $now,
            ]);
        }

        foreach ($directorates as $index => $directorate) {
            $this->createUser([
                'name' => $directorate['name'].' Officer',
                'service_number' => 'NIS/DIR/'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                'email' => strtolower('directorate.'.$directorate['code'].'.seed@nis.gov.ng'),
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

            $this->createUser([
                'name' => $directorate['name'].' Desk Admin',
                'service_number' => 'NIS/DDM/'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                'email' => strtolower('directorate-admin.'.$directorate['code'].'.seed@nis.gov.ng'),
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

        foreach ($zones as $index => $zone) {
            $this->createUser([
                'name' => $zone['name'].' Zonal Commander',
                'service_number' => 'NIS/ZNL/'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                'email' => strtolower('zonal.'.$zone['code'].'.seed@nis.gov.ng'),
                'password' => $password,
                'role' => 'zonal',
                'user_category' => 'zonal_commander',
                'primary_location_type' => 'zonal',
                'primary_location_code' => $zone['code'],
                'geo_state' => 'FC',
                'access_level' => 4,
                'assigned_zonal_command_code' => $zone['code'],
                'email_verified_at' => $now,
            ]);

            $this->createUser([
                'name' => $zone['name'].' Supervisor',
                'service_number' => 'NIS/SUP/'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                'email' => strtolower('supervisor.'.$zone['code'].'.seed@nis.gov.ng'),
                'password' => $password,
                'role' => 'state',
                'user_category' => 'desk_admin',
                'primary_location_type' => 'state',
                'primary_location_code' => $zone['code'],
                'geo_state' => 'FC',
                'access_level' => 2,
                'assigned_zonal_command_code' => $zone['code'],
                'email_verified_at' => $now,
            ]);
        }

        foreach (range(1, 10) as $index) {
            $unitCode = 'CGIS-'.str_pad((string) $index, 2, '0', STR_PAD_LEFT);

            $this->createUser([
                'name' => 'CGIS Unit '.$index,
                'service_number' => 'NIS/CGI/'.str_pad((string) $index, 3, '0', STR_PAD_LEFT),
                'email' => strtolower('hq.'.$unitCode.'.seed@nis.gov.ng'),
                'password' => $password,
                'role' => 'admin',
                'user_category' => 'admin',
                'primary_location_type' => 'headquarters',
                'primary_location_code' => 'HQ',
                'geo_state' => 'FC',
                'access_level' => 5,
                'assigned_cgis_unit_code' => $unitCode,
                'email_verified_at' => $now,
            ]);
        }

        foreach (range(1, 5) as $index) {
            $this->createUser([
                'name' => 'HQ Admin '.$index,
                'service_number' => 'NIS/HQA/'.str_pad((string) (100 + $index), 3, '0', STR_PAD_LEFT),
                'email' => strtolower('admin'.$index.'.seed@nis.gov.ng'),
                'password' => $password,
                'role' => 'admin',
                'user_category' => 'admin',
                'primary_location_type' => 'headquarters',
                'primary_location_code' => 'HQ',
                'geo_state' => 'FC',
                'access_level' => 5,
                'email_verified_at' => $now,
            ]);
        }

        $this->createUser([
            'name' => 'Super Admin User',
            'service_number' => 'NIS/SA/900',
            'email' => 'superadmin.seed@nis.gov.ng',
            'password' => $password,
            'role' => 'admin',
            'user_category' => 'super_admin',
            'primary_location_type' => 'headquarters',
            'primary_location_code' => 'HQ',
            'geo_state' => 'FC',
            'access_level' => 6,
            'email_verified_at' => $now,
        ]);
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
