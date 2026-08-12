<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fix invalid legacy datetime rows that break strict-mode ALTER TABLE on users.
     */
    public function up(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'email_verified_at') || DB::getDriverName() !== 'mysql') {
            return;
        }

        $sqlMode = DB::scalar("SELECT @@SESSION.sql_mode");

        DB::statement("SET SESSION sql_mode = ''");
        DB::statement("UPDATE `users` SET `email_verified_at` = NULL WHERE `email_verified_at` = '0000-00-00 00:00:00'");
        DB::statement("SET SESSION sql_mode = ?", [$sqlMode]);
    }

    public function down(): void
    {
        // Intentionally left as no-op to avoid restoring invalid datetime values.
    }
};
