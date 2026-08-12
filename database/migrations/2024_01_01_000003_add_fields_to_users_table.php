<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            if (! Schema::hasColumn('users', 'service_number')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('service_number')->nullable()->unique()->after('name');
                });
            }

            if (! Schema::hasColumn('users', 'role')) {
                Schema::table('users', function (Blueprint $table) {
                    if (DB::getDriverName() === 'mysql') {
                        $table->enum('role', ['admin', 'zonal', 'state', 'officer'])->default('officer')->after('service_number');
                    } else {
                        $table->string('role', 20)->default('officer')->after('service_number');
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['service_number', 'role']);
        });
    }
};
