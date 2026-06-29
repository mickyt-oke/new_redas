<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('otp_codes')) {
            Schema::create('otp_codes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('purpose')->index(); // login, verify_email, etc
                $table->string('code_hash');
                $table->timestamp('expires_at')->index();
                $table->unsignedInteger('attempts')->default(0);
                $table->timestamp('consumed_at')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'purpose']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
?>
