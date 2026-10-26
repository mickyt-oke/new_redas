<?php

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hash any plaintext MFA backup codes that were stored before the
     * application started storing SHA-256 hashes.
     *
     * The raw column values are read directly so an APP_KEY rotation
     * (for example on Laravel Cloud) does not cause the migration to
     * fail when old rows cannot be decrypted. Rows that fail to decrypt
     * are logged and skipped rather than crashing the deployment.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // No schema change; only data migration.
        });

        if (! Schema::hasColumn('users', 'mfa_backup_codes')) {
            return;
        }

        DB::table('users')
            ->whereNotNull('mfa_backup_codes')
            ->chunkById(100, function ($rows) {
                foreach ($rows as $row) {
                    $raw = $row->mfa_backup_codes;

                    if (blank($raw)) {
                        continue;
                    }

                    try {
                        $codes = json_decode(Crypt::decryptString($raw), true);
                    } catch (DecryptException $e) {
                        Log::warning("Skipping user {$row->id}: unable to decrypt mfa_backup_codes.");

                        continue;
                    }

                    if (! is_array($codes) || $codes === []) {
                        continue;
                    }

                    $hashed = [];
                    $changed = false;

                    foreach ($codes as $code) {
                        $code = (string) $code;

                        if (strlen($code) === 64) {
                            $hashed[] = $code;
                        } else {
                            $hashed[] = hash('sha256', $code);
                            $changed = true;
                        }
                    }

                    if ($changed) {
                        DB::table('users')
                            ->where('id', $row->id)
                            ->update([
                                'mfa_backup_codes' => Crypt::encryptString(json_encode($hashed)),
                                'updated_at' => now(),
                            ]);
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
