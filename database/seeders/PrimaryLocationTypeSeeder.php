<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrimaryLocationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $types = [
            ['name' => 'state', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('primary_location_types')->upsert(
            $types,
            ['name'],
            ['updated_at']
        );
    }
}
