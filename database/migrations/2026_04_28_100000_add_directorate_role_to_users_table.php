<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite doesn't support MySQL's "ALTER TABLE ... MODIFY COLUMN" / ENUM changes.
        // For sqlite tests, we can safely skip this schema tweak because role values remain usable as strings.
        if (env('DB_CONNECTION') !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'zonal', 'state', 'officer', 'directorate') DEFAULT 'officer'");
    }

    public function down(): void
    {
        if (env('DB_CONNECTION') !== 'mysql') {
            return;
        }

        DB::statement("UPDATE users SET role = 'officer' WHERE role = 'directorate'");
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'zonal', 'state', 'officer') DEFAULT 'officer'");
    }
};
