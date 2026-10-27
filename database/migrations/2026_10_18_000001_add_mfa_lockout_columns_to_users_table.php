<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('mfa_failed_attempts')->default(0)->after('mfa_last_used_at');
            $table->timestamp('mfa_locked_until')->nullable()->after('mfa_failed_attempts');
            $table->unsignedBigInteger('mfa_last_timestep')->nullable()->after('mfa_locked_until');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['mfa_failed_attempts', 'mfa_locked_until', 'mfa_last_timestep']);
        });
    }
};
