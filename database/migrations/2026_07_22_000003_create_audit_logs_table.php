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
        if (Schema::hasTable('audit_logs')) {
            return;
        }

        Schema::create('audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 100);
            $table->string('entity_type', 100)->nullable();
            $table->string('entity_id', 100)->nullable();
            $table->json('details')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('location', 120)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('status', 20)->default('success');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['action', 'created_at'], 'audit_logs_action_created_idx');
            $table->index(['status', 'created_at'], 'audit_logs_status_created_idx');
            $table->index(['user_id', 'created_at'], 'audit_logs_user_created_idx');
            $table->index(['entity_type', 'entity_id'], 'audit_logs_entity_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
