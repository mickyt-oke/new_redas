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
        if (!Schema::hasTable('applications')) {
            Schema::create('applications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('status')->default('draft');
                $table->string('type')->default('visa');
                $table->string('period')->nullable();
                $table->json('return_data')->nullable();
                $table->foreignId('supervisor_id')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
                $table->text('comments')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
