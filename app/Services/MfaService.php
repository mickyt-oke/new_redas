<?php

namespace App\Services;

use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use PragmaRX\Google2FA\Google2FA;

class MfaService
{
    private const MAX_FAILED_ATTEMPTS = 5;

    private const LOCKOUT_MINUTES = 15;

    private const TIMESTEP_SECONDS = 30;

    public function __construct(private Google2FA $google2fa) {}

    /**
     * Generate a new TOTP secret.
     */
    public function generateSecret(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    /**
     * Build the otpauth:// provisioning URI for the given user and secret.
     */
    public function getQrCodeUrl(User $user, string $secret): string
    {
        return $this->google2fa->getQRCodeUrl(
            config('app.name', 'NIS-REDAS'),
            (string) $user->email,
            $secret
        );
    }

    /**
     * Render an SVG QR code for the given otpauth URL.
     */
    public function renderQrCodeSvg(string $url): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(192, 0),
            new SvgImageBackEnd
        );

        $writer = new Writer($renderer);

        return $writer->writeString($url);
    }

    /**
     * Generate a batch of single-use backup codes.
     *
     * @return list<string>
     */
    public function generateBackupCodes(int $count = 8): array
    {
        $codes = [];

        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(4)));
        }

        return $codes;
    }

    /**
     * Hash plaintext backup codes for storage.
     *
     * Codes are stored as SHA-256 hashes so a database breach does not reveal
     * usable recovery codes. The plaintext codes are shown to the user exactly
     * once during MFA setup.
     *
     * @param  list<string>  $codes
     * @return list<string>
     */
    public function hashBackupCodes(array $codes): array
    {
        return array_values(array_map(
            fn (string $code) => hash('sha256', $code),
            $codes
        ));
    }

    /**
     * Verify a TOTP code or single-use backup code for the user.
     */
    public function verifyCode(User $user, string $code): bool
    {
        $code = preg_replace('/\s+/', '', $code) ?? '';

        if ($code === '') {
            return false;
        }

        if ($this->isLocked($user)) {
            return false;
        }

        $secret = $user->mfa_secret;

        if (empty($secret)) {
            return false;
        }

        // Standard 6-digit TOTP codes are numeric; backup codes are alphanumeric.
        if (ctype_digit($code) && strlen($code) === 6) {
            if ($this->google2fa->verifyKey($secret, $code, 1)) {
                if ($this->isTimestepReplay($user)) {
                    return false;
                }

                $this->recordUse($user);

                return true;
            }
        }

        if ($this->verifyBackupCode($user, $code)) {
            $this->recordUse($user);

            return true;
        }

        return false;
    }

    /**
     * Get the current TOTP code for a secret (useful in tests).
     */
    public function currentCode(string $secret): string
    {
        return $this->google2fa->getCurrentOtp($secret);
    }

    /**
     * Verify and consume a backup code.
     */
    private function verifyBackupCode(User $user, string $code): bool
    {
        $codes = $user->mfa_backup_codes;

        if (! is_array($codes) || $codes === []) {
            return false;
        }

        $codeHash = hash('sha256', $code);
        $index = array_search($codeHash, $codes, true);

        if ($index === false) {
            return false;
        }

        array_splice($codes, (int) $index, 1);

        $user->forceFill([
            'mfa_backup_codes' => $codes === [] ? null : $codes,
        ])->save();

        return true;
    }

    /**
     * Record that MFA was used for this account.
     */
    private function recordUse(User $user): void
    {
        $user->forceFill([
            'mfa_last_used_at' => now(),
            'mfa_last_timestep' => $this->currentTimestep(),
            'mfa_failed_attempts' => 0,
            'mfa_locked_until' => null,
        ])->save();
    }

    /**
     * Record a failed MFA attempt and lock the account after the threshold.
     */
    public function recordFailedAttempt(User $user): void
    {
        $attempts = (int) $user->mfa_failed_attempts + 1;

        if ($attempts >= self::MAX_FAILED_ATTEMPTS) {
            $user->forceFill([
                'mfa_failed_attempts' => 0,
                'mfa_locked_until' => now()->addMinutes(self::LOCKOUT_MINUTES),
            ])->save();

            return;
        }

        $user->forceFill([
            'mfa_failed_attempts' => $attempts,
        ])->save();
    }

    /**
     * Reset the MFA lockout counters for the account.
     */
    public function resetLockout(User $user): void
    {
        if ($user->mfa_failed_attempts !== 0
            || $user->mfa_locked_until !== null
            || $user->mfa_last_timestep !== null) {
            $user->forceFill([
                'mfa_failed_attempts' => 0,
                'mfa_locked_until' => null,
                'mfa_last_timestep' => null,
            ])->save();
        }
    }

    /**
     * Whether the account is currently locked out from MFA verification.
     */
    public function isLocked(User $user): bool
    {
        return $user->mfa_locked_until !== null
            && $user->mfa_locked_until->isFuture();
    }

    /**
     * Whether the current TOTP timestep has already been used for this account.
     */
    private function isTimestepReplay(User $user): bool
    {
        $lastTimestep = $user->mfa_last_timestep;

        if ($lastTimestep === null) {
            return false;
        }

        return $this->currentTimestep() <= $lastTimestep;
    }

    /**
     * The current TOTP timestep for the system clock.
     */
    private function currentTimestep(): int
    {
        return (int) floor(time() / self::TIMESTEP_SECONDS);
    }
}
