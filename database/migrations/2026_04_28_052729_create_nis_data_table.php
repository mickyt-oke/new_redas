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
        if (! Schema::hasTable('nis_data')) {
            Schema::create('nis_data', function (Blueprint $table) {
                $table->id();
                $table->unsignedTinyInteger('directorate_id');
                $table->string('cadre');
                $table->unsignedInteger('male')->default(0);
                $table->unsignedInteger('female')->default(0);
                $table->unsignedInteger('total')->default(0);
                $table->string('zone')->nullable();
                $table->date('period');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nis_data');
    }
};
