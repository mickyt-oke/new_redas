<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (env('DB_CONNECTION') !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(20) NOT NULL DEFAULT 'officer'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (env('DB_CONNECTION') !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'zonal', 'state', 'officer', 'directorate') DEFAULT 'officer'");
    }
};
