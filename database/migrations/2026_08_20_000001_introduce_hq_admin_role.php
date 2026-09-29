<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Introduce the hq_admin category: existing HQ approver accounts
     * (user_category = 'admin') become hq_admin; the 'admin' category is
     * repurposed as the view-only general administrator. Submissions that
     * already passed HQ review and sit in the legacy admin_review stage are
     * finalized as approved.
     */
    public function up(): void
    {
        DB::table('users')
            ->where('user_category', 'admin')
            ->update(['user_category' => 'hq_admin']);

        DB::table('applications')
            ->where('workflow_stage', 'admin_review')
            ->where('status', '!=', 'approved')
            ->update(['workflow_stage' => 'approved', 'status' => 'approved']);
    }

    public function down(): void
    {
        DB::table('users')
            ->where('user_category', 'hq_admin')
            ->update(['user_category' => 'admin']);
    }
};
