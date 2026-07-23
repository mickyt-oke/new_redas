<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrimaryLocationCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $states = [
            ['code' => 'AB', 'location_name' => 'Abia', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'AD', 'location_name' => 'Adamawa', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'AK', 'location_name' => 'Akwa Ibom', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'AN', 'location_name' => 'Anambra', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'BA', 'location_name' => 'Bauchi', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'BY', 'location_name' => 'Bayelsa', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'BE', 'location_name' => 'Benue', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'BO', 'location_name' => 'Borno', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'CR', 'location_name' => 'Cross River', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'DE', 'location_name' => 'Delta', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EB', 'location_name' => 'Ebonyi', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'ED', 'location_name' => 'Edo', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EK', 'location_name' => 'Ekiti', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EN', 'location_name' => 'Enugu', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'FC', 'location_name' => 'FCT', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'GO', 'location_name' => 'Gombe', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'IM', 'location_name' => 'Imo', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'JI', 'location_name' => 'Jigawa', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'KD', 'location_name' => 'Kaduna', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'KN', 'location_name' => 'Kano', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'KT', 'location_name' => 'Katsina', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'KE', 'location_name' => 'Kebbi', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'KO', 'location_name' => 'Kogi', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'KW', 'location_name' => 'Kwara', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'LA', 'location_name' => 'Lagos', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'NA', 'location_name' => 'Nasarawa', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'NI', 'location_name' => 'Niger', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'OG', 'location_name' => 'Ogun', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'ON', 'location_name' => 'Ondo', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'OS', 'location_name' => 'Osun', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'OY', 'location_name' => 'Oyo', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'PL', 'location_name' => 'Plateau', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'RI', 'location_name' => 'Rivers', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'SO', 'location_name' => 'Sokoto', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'TA', 'location_name' => 'Taraba', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'YO', 'location_name' => 'Yobe', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'ZA', 'location_name' => 'Zamfara', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('primary_location_codes')->upsert(
            $states,
            ['code'],
            ['location_name', 'updated_at']
        );
    }
}
