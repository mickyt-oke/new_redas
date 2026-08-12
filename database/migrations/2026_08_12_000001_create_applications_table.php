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
        if (Schema::hasTable('applications')) {
            return;
        }

        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status', 40)->default('pending');
            $table->string('workflow_stage', 60)->default('submitted');
            $table->json('workflow_path')->nullable();
            $table->string('category', 20)->nullable();
            $table->string('scope_code', 50)->nullable();
            $table->string('zonal_code', 50)->nullable();
            $table->json('return_data')->nullable();
            $table->text('comments')->nullable();
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('last_action_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['workflow_stage', 'scope_code'], 'applications_stage_scope_idx');
            $table->index(['workflow_stage', 'zonal_code'], 'applications_stage_zonal_idx');
            $table->index(['category', 'workflow_stage'], 'applications_category_stage_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
