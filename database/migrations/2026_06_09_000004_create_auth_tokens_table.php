<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('auth_tokens')) {
            Schema::create('auth_tokens', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('type')->index(); // email_verify, password_reset
                $table->string('token_hash')->index();
                $table->timestamp('expires_at')->index();
                $table->timestamp('used_at')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'type']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_tokens');
    }
};
