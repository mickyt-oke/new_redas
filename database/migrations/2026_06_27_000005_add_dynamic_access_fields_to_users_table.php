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
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            if (!Schema::hasColumn('users', 'user_category')) {
                $table->string('user_category', 40)
                    ->default('state_user')
                    ->after('role');
            }

            if (!Schema::hasColumn('users', 'primary_location_type')) {
                $table->string('primary_location_type', 20)
                    ->default('state')
                    ->after('user_category');
            }

            if (!Schema::hasColumn('users', 'primary_location_code')) {
                $table->string('primary_location_code', 50)
                    ->nullable()
                    ->after('primary_location_type');
            }

            if (!Schema::hasColumn('users', 'access_level')) {
                $table->unsignedTinyInteger('access_level')
                    ->default(0)
                    ->after('primary_location_code');
            }

            if (Schema::hasColumn('users', 'user_category') && Schema::hasColumn('users', 'primary_location_type')) {
                $table->index(['user_category', 'primary_location_type'], 'users_category_location_idx');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'access_level')) {
                $table->dropColumn('access_level');
            }

            if (Schema::hasColumn('users', 'primary_location_code')) {
                $table->dropColumn('primary_location_code');
            }

            if (Schema::hasColumn('users', 'primary_location_type')) {
                $table->dropColumn('primary_location_type');
            }

            if (Schema::hasColumn('users', 'user_category')) {
                $table->dropColumn('user_category');
            }

            if (
                Schema::hasColumn('users', 'user_category')
                && Schema::hasColumn('users', 'primary_location_type')
            ) {
                $table->dropIndex('users_category_location_idx');
            }
        });
    }
};
