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
        if (!Schema::hasTable('primary_location_types')) {
            Schema::create('primary_location_types', function (Blueprint $table): void {
                $table->id();
                $table->string('name', 100)->unique();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('primary_location_codes')) {
            Schema::create('primary_location_codes', function (Blueprint $table): void {
                $table->id();
                $table->string('code', 10)->unique();
                $table->string('location_name', 100);
                $table->timestamps();

                $table->index('location_name', 'primary_location_codes_location_name_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('primary_location_codes')) {
            Schema::drop('primary_location_codes');
        }

        if (Schema::hasTable('primary_location_types')) {
            Schema::drop('primary_location_types');
        }
    }
};
