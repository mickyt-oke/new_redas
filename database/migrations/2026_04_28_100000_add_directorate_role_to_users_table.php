<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'zonal', 'state', 'officer', 'directorate') DEFAULT 'officer'");
    }

    public function down(): void
    {
        DB::statement("UPDATE users SET role = 'officer' WHERE role = 'directorate'");
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'zonal', 'state', 'officer') DEFAULT 'officer'");
    }
};
