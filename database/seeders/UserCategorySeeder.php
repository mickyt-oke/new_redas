<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserCategorySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed one account for every recognised user category.
     *
     * All accounts share the default password "password123" and are verified
     * so they can log in immediately.
     */
    public function run(): void
    {
        $now = now();
        $password = Hash::make('password123');

        $users = [
            [
                'name' => 'State User',
                'email' => 'state.user@nis.gov.ng',
                'service_number' => 'NIS/ST/001',
                'user_category' => 'state_user',
                'role' => 'officer',
                'primary_location_type' => 'state',
                'primary_location_code' => 'LA',
                'geo_state' => 'LA',
                'access_level' => 0,
            ],
            [
                'name' => 'Desk Admin',
                'email' => 'desk.admin@nis.gov.ng',
                'service_number' => 'NIS/DA/002',
                'user_category' => 'desk_admin',
                'role' => 'state',
                'primary_location_type' => 'state',
                'primary_location_code' => 'LA',
                'geo_state' => 'LA',
                'access_level' => 1,
            ],
            [
                'name' => 'Directorate User',
                'email' => 'directorate.user@nis.gov.ng',
                'service_number' => 'NIS/DU/003',
                'user_category' => 'directorate_user',
                'role' => 'directorate',
                'primary_location_type' => 'directorate',
                'primary_location_code' => 'HRM',
                'assigned_directorate_code' => 'HRM',
                'access_level' => 0,
            ],
            [
                'name' => 'Directorate Admin',
                'email' => 'directorate.admin@nis.gov.ng',
                'service_number' => 'NIS/DA/004',
                'user_category' => 'directorate_admin',
                'role' => 'admin',
                'primary_location_type' => 'directorate',
                'primary_location_code' => 'HRM',
                'assigned_directorate_code' => 'HRM',
                'access_level' => 3,
            ],
            [
                'name' => 'Zonal User',
                'email' => 'zonal.user@nis.gov.ng',
                'service_number' => 'NIS/ZU/005',
                'user_category' => 'zonal_user',
                'role' => 'user',
                'primary_location_type' => 'zonal',
                'primary_location_code' => 'ZONE-A',
                'access_level' => 0,
            ],
            [
                'name' => 'Zonal Commander',
                'email' => 'zonal.commander@nis.gov.ng',
                'service_number' => 'NIS/ZC/006',
                'user_category' => 'zonal_commander',
                'role' => 'zonal',
                'primary_location_type' => 'zonal',
                'primary_location_code' => 'ZONE-A',
                'access_level' => 4,
            ],
            [
                'name' => 'CGIS Unit User',
                'email' => 'cgis.unit@nis.gov.ng',
                'service_number' => 'NIS/CU/007',
                'user_category' => 'cgis_unit_user',
                'role' => 'unit_officer',
                'primary_location_type' => 'unit',
                'primary_location_code' => 'actu',
                'assigned_cgis_unit_code' => 'actu',
                'access_level' => 0,
            ],
            [
                'name' => 'CGIS Desk Admin',
                'email' => 'cgis.desk@nis.gov.ng',
                'service_number' => 'NIS/CD/008',
                'user_category' => 'cgis_desk_admin',
                'role' => 'unit_admin',
                'primary_location_type' => 'unit',
                'primary_location_code' => 'actu',
                'assigned_cgis_unit_code' => 'actu',
                'access_level' => 2,
            ],
            [
                'name' => 'HQ Admin',
                'email' => 'hq.admin@nis.gov.ng',
                'service_number' => 'NIS/HA/009',
                'user_category' => 'hq_admin',
                'role' => 'admin',
                'primary_location_type' => 'headquarters',
                'primary_location_code' => 'HQ',
                'access_level' => 5,
            ],
            [
                'name' => 'General Admin',
                'email' => 'general.admin@nis.gov.ng',
                'service_number' => 'NIS/GA/010',
                'user_category' => 'admin',
                'role' => 'admin',
                'primary_location_type' => 'headquarters',
                'primary_location_code' => 'HQ',
                'access_level' => 5,
            ],
            [
                'name' => 'Super Admin',
                'email' => 'super.admin@nis.gov.ng',
                'service_number' => 'NIS/SA/011',
                'user_category' => 'super_admin',
                'role' => 'admin',
                'primary_location_type' => 'headquarters',
                'primary_location_code' => 'HQ',
                'access_level' => 6,
            ],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                array_merge($user, [
                    'password' => $password,
                    'email_verified_at' => $now,
                    'is_enabled' => true,
                ])
            );
        }
    }
}
