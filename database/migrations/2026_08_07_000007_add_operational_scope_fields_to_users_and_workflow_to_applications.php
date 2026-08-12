<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add explicit user provisioning fields and workflow tracking columns.
     */
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            // Strict MySQL rejects '0000-00-00 00:00:00' values when the
            // users table is read or rebuilt, so sanitize them first.
            if (Schema::hasColumn('users', 'email_verified_at') && DB::getDriverName() === 'mysql') {
                $sqlMode = DB::scalar("SELECT @@SESSION.sql_mode");

                DB::statement("SET SESSION sql_mode = ''");
                DB::statement("UPDATE `users` SET `email_verified_at` = NULL WHERE `email_verified_at` = '0000-00-00 00:00:00'");
                DB::statement("SET SESSION sql_mode = ?", [$sqlMode]);
            }

            Schema::table('users', function (Blueprint $table): void {
                if (! Schema::hasColumn('users', 'assigned_state_code')) {
                    $table->string('assigned_state_code', 50)->nullable()->after('primary_location_code');
                }

                if (! Schema::hasColumn('users', 'assigned_directorate_code')) {
                    $table->string('assigned_directorate_code', 50)->nullable()->after('assigned_state_code');
                }

                if (! Schema::hasColumn('users', 'assigned_cgis_unit_code')) {
                    $table->string('assigned_cgis_unit_code', 50)->nullable()->after('assigned_directorate_code');
                }

                if (! Schema::hasColumn('users', 'assigned_desk_admin_code')) {
                    $table->string('assigned_desk_admin_code', 50)->nullable()->after('assigned_cgis_unit_code');
                }

                if (! Schema::hasColumn('users', 'assigned_zonal_command_code')) {
                    $table->string('assigned_zonal_command_code', 50)->nullable()->after('assigned_desk_admin_code');
                }
            });
        }

        if (Schema::hasTable('applications')) {
            Schema::table('applications', function (Blueprint $table): void {
                if (! Schema::hasColumn('applications', 'workflow_stage')) {
                    $table->string('workflow_stage', 60)->default('create_returns')->after('status');
                }

                if (! Schema::hasColumn('applications', 'workflow_path')) {
                    $table->json('workflow_path')->nullable()->after('workflow_stage');
                }

                if (! Schema::hasColumn('applications', 'last_action_by')) {
                    $table->unsignedBigInteger('last_action_by')->nullable()->after('workflow_path');
                }
            });
        }
    }

    /**
     * Rollback added provisioning and workflow columns.
     */
    public function down(): void
    {
        if (Schema::hasTable('applications')) {
            Schema::table('applications', function (Blueprint $table): void {
                if (Schema::hasColumn('applications', 'last_action_by')) {
                    $table->dropColumn('last_action_by');
                }
                if (Schema::hasColumn('applications', 'workflow_path')) {
                    $table->dropColumn('workflow_path');
                }
                if (Schema::hasColumn('applications', 'workflow_stage')) {
                    $table->dropColumn('workflow_stage');
                }
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table): void {
                if (Schema::hasColumn('users', 'assigned_zonal_command_code')) {
                    $table->dropColumn('assigned_zonal_command_code');
                }
                if (Schema::hasColumn('users', 'assigned_desk_admin_code')) {
                    $table->dropColumn('assigned_desk_admin_code');
                }
                if (Schema::hasColumn('users', 'assigned_cgis_unit_code')) {
                    $table->dropColumn('assigned_cgis_unit_code');
                }
                if (Schema::hasColumn('users', 'assigned_directorate_code')) {
                    $table->dropColumn('assigned_directorate_code');
                }
                if (Schema::hasColumn('users', 'assigned_state_code')) {
                    $table->dropColumn('assigned_state_code');
                }
            });
        }
    }
};
