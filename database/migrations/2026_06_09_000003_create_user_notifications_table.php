<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('user_notifications')) {
            Schema::create('user_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();

                $table->string('type')->index(); // success, info, warning, danger
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->string('tag')->nullable(); // e.g. Urgent, Deadline, Approved

                $table->string('action_url')->nullable();

                $table->boolean('is_read')->default(false);

                $table->json('payload_json')->nullable();
                $table->json('error')->nullable();

                $table->timestamps();

                $table->index(['user_id', 'is_read', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
    }
};
