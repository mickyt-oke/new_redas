<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hash any plaintext MFA backup codes that were stored before the
     * application started storing SHA-256 hashes.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // No schema change; only data migration.
        });

        if (! Schema::hasColumn('users', 'mfa_backup_codes')) {
            return;
        }

        User::query()
            ->whereNotNull('mfa_backup_codes')
            ->chunkById(100, function ($users) {
                foreach ($users as $user) {
                    $codes = $user->mfa_backup_codes;

                    if (! is_array($codes) || $codes === []) {
                        continue;
                    }

                    $hashed = [];
                    foreach ($codes as $code) {
                        $hashed[] = strlen((string) $code) === 64
                            ? (string) $code
                            : hash('sha256', (string) $code);
                    }

                    if ($hashed !== $codes) {
                        $user->forceFill(['mfa_backup_codes' => $hashed])->save();
                    }
                }
            });
    }

    /**
     * Reverse the migration.
     *
     * Backup codes cannot be reversed to plaintext once hashed.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // No-op: hashed codes cannot be restored to plaintext.
        });
    }
};
