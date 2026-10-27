<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Console\Command;

class MfaResetCommand extends Command
{
    protected $signature = 'redas:mfa-reset {service_number}';

    protected $description = 'Reset MFA enrolment for a user by service number.';

    public function handle(): int
    {
        $serviceNumber = (string) $this->argument('service_number');

        /** @var User|null $user */
        $user = User::query()->where('service_number', $serviceNumber)->first();

        if (! $user) {
            $this->error("No user found with service number {$serviceNumber}.");

            return self::FAILURE;
        }

        $user->forceFill([
            'mfa_secret' => null,
            'mfa_backup_codes' => null,
            'mfa_enabled' => false,
            'mfa_verified_at' => null,
            'mfa_failed_attempts' => 0,
            'mfa_locked_until' => null,
            'mfa_last_timestep' => null,
        ])->save();

        AuditLogger::log(
            user: $user,
            action: 'security.mfa_reset',
            status: 'success',
            entityType: 'user',
            entityId: $user->id,
            details: [
                'service_number' => $user->service_number,
                'trigger' => 'console',
            ]
        );

        $this->info("MFA enrolment reset for {$user->name} ({$user->service_number}).");

        return self::SUCCESS;
    }
}
