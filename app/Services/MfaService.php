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
     * Verify a TOTP code or single-use backup code for the user.
     */
    public function verifyCode(User $user, string $code): bool
    {
        $code = preg_replace('/\s+/', '', $code) ?? '';

        if ($code === '') {
            return false;
        }

        $secret = $user->mfa_secret;

        if (empty($secret)) {
            return false;
        }

        // Standard 6-digit TOTP codes are numeric; backup codes are alphanumeric.
        if (ctype_digit($code) && strlen($code) === 6) {
            if ($this->google2fa->verifyKey($secret, $code, 1)) {
                $this->recordUse($user);

                return true;
            }
        }

        return $this->verifyBackupCode($user, $code);
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

        $index = array_search($code, $codes, true);

        if ($index === false) {
            return false;
        }

        array_splice($codes, (int) $index, 1);

        $user->forceFill([
            'mfa_backup_codes' => $codes === [] ? null : $codes,
        ])->save();

        $this->recordUse($user);

        return true;
    }

    /**
     * Record that MFA was used for this account.
     */
    private function recordUse(User $user): void
    {
        $user->forceFill([
            'mfa_last_used_at' => now(),
        ])->save();
    }
}
