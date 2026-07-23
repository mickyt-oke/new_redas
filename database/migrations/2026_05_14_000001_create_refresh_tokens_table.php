<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refresh_tokens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // JWT ID (jti) for refresh token rotation
            $table->string('jti', 64)->unique();

            // Store only a hash (never plaintext refresh token)
            $table->string('token_hash', 64)->unique();

            $table->timestamp('expires_at')->index();
            $table->timestamp('revoked_at')->nullable()->index();

            // Hardening / audit
            $table->string('created_ip', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            // Helpful composite index for lookup by user
            $table->index(['user_id', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refresh_tokens');
    }
};
