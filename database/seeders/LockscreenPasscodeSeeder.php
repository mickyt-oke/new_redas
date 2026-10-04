<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LockscreenPasscodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update users who have no lockscreen_passcode to use their main password hash
        // We use a raw query to efficiently copy the password column to lockscreen_passcode for null values
        DB::table('users')
            ->whereNull('lockscreen_passcode')
            ->update([
                'lockscreen_passcode' => DB::raw('password'),
            ]);
    }
}
