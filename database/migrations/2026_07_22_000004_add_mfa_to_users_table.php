<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'mfa_secret')) {
                $table->text('mfa_secret')->nullable()->after('geo_state');
            }

            if (! Schema::hasColumn('users', 'mfa_enabled')) {
                $table->boolean('mfa_enabled')->default(false)->after('mfa_secret');
            }

            if (! Schema::hasColumn('users', 'mfa_verified_at')) {
                $table->timestamp('mfa_verified_at')->nullable()->after('mfa_enabled');
            }

            if (! Schema::hasColumn('users', 'mfa_backup_codes')) {
                $table->text('mfa_backup_codes')->nullable()->after('mfa_verified_at');
            }

            if (! Schema::hasColumn('users', 'mfa_last_used_at')) {
                $table->timestamp('mfa_last_used_at')->nullable()->after('mfa_backup_codes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'mfa_last_used_at')) {
                $table->dropColumn('mfa_last_used_at');
            }

            if (Schema::hasColumn('users', 'mfa_backup_codes')) {
                $table->dropColumn('mfa_backup_codes');
            }

            if (Schema::hasColumn('users', 'mfa_verified_at')) {
                $table->dropColumn('mfa_verified_at');
            }

            if (Schema::hasColumn('users', 'mfa_enabled')) {
                $table->dropColumn('mfa_enabled');
            }

            if (Schema::hasColumn('users', 'mfa_secret')) {
                $table->dropColumn('mfa_secret');
            }
        });
    }
};
