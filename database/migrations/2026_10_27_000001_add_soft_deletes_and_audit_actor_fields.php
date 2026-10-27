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
        // M-6 / S-7 — soft deletes on records of record.
        Schema::table('applications', function (Blueprint $table) {
            $table->softDeletes();
            $table->timestamp('retention_expires_at')->nullable()->after('updated_at');
            $table->index('deleted_at', 'applications_deleted_at_idx');
        });

        Schema::table('application_comments', function (Blueprint $table) {
            $table->softDeletes();
            $table->index('deleted_at', 'application_comments_deleted_at_idx');
        });

        // M-6 — deleting a user must not destroy their returns or the review trail.
        Schema::table('applications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->restrictOnDelete();
        });

        // M-6 / S-4 — denormalise actor identity into audit logs so attribution
        // survives account removal.
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->string('actor_name', 255)->nullable()->after('user_id');
            $table->string('actor_email', 255)->nullable()->after('actor_name');
            $table->string('actor_category', 50)->nullable()->after('actor_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropColumn(['actor_name', 'actor_email', 'actor_category']);
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->dropIndex('applications_deleted_at_idx');
            $table->dropColumn(['deleted_at', 'retention_expires_at']);
        });

        Schema::table('application_comments', function (Blueprint $table) {
            $table->dropIndex('application_comments_deleted_at_idx');
            $table->dropColumn('deleted_at');
        });
    }
};
