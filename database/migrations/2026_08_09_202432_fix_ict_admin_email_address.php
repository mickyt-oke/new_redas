<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix any user having 10010 in email or service_number 10010 to have ict-admin@nis.gov.ng
        User::where('service_number', '10010')
            ->orWhere('email', 'like', '%10010%')
            ->update([
                'email' => 'ict-admin@nis.gov.ng'
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse required
    }
};
