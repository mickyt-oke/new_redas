<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\MfaService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MfaController extends Controller
{
    public function __construct(
        private MfaService $mfaService,
        private AuthController $authController,
    ) {}

    /**
     * Show the MFA setup screen with a fresh TOTP secret and QR code.
     */
    public function setup(Request $request): View|RedirectResponse
    {
        $user = $this->pendingUser($request);

        if ($user->isMfaEnabled()) {
            return redirect()->route('mfa.challenge');
        }

        if (! $user->hasMfaSecret()) {
            $user->forceFill([
                'mfa_secret' => $this->mfaService->generateSecret(),
            ])->save();
        }

        $secret = $user->mfa_secret;
        $qrUrl = $this->mfaService->getQrCodeUrl($user, $secret);
        $qrSvg = $this->mfaService->renderQrCodeSvg($qrUrl);

        return view('mfa.setup', [
            'user' => $user,
            'secret' => $secret,
            'qrUrl' => $qrUrl,
            'qrSvg' => $qrSvg,
        ]);
    }

    /**
     * Verify the first TOTP code and enable MFA for the pending user.
     */
    public function verifySetup(Request $request): View|RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|max:255',
        ]);

        $user = $this->pendingUser($request);
        $code = (string) $request->input('code');

        if (! $this->mfaService->verifyCode($user, $code)) {
            return back()->withErrors([
                'code' => 'The verification code is invalid. Please try again.',
            ]);
        }

        $backupCodes = $this->mfaService->generateBackupCodes();

        $user->forceFill([
            'mfa_enabled' => true,
            'mfa_verified_at' => now(),
            'mfa_backup_codes' => $backupCodes,
        ])->save();

        $request->session()->put('mfa.setup_verified', true);

        return view('mfa.backup-codes', [
            'user' => $user,
            'codes' => $backupCodes,
        ]);
    }

    /**
     * Complete login after setup verification and backup-code presentation.
     */
    public function complete(Request $request): RedirectResponse
    {
        if (! $request->session()->get('mfa.setup_verified')) {
            return redirect()->route('login');
        }

        $user = $this->pendingUser($request);

        return $this->authController->completeLogin(
            $request,
            $user,
            $this->locationFor($request, $user),
            (bool) $request->session()->get('mfa.remember', false)
        );
    }

    /**
     * Show the MFA challenge screen for users that already have MFA enabled.
     */
    public function challenge(Request $request): View|RedirectResponse
    {
        $user = $this->pendingUser($request);

        if (! $user->isMfaEnabled()) {
            return redirect()->route('mfa.setup');
        }

        return view('mfa.challenge', [
            'user' => $user,
        ]);
    }

    /**
     * Verify a TOTP or backup code for an MFA-enabled user.
     */
    public function verifyChallenge(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|max:255',
        ]);

        $user = $this->pendingUser($request);
        $code = (string) $request->input('code');

        if (! $this->mfaService->verifyCode($user, $code)) {
            return back()->withErrors([
                'code' => 'The authentication code is invalid. Please try again.',
            ]);
        }

        return $this->authController->completeLogin(
            $request,
            $user,
            $this->locationFor($request, $user),
            (bool) $request->session()->get('mfa.remember', false)
        );
    }

    /**
     * Cancel the pending MFA flow and return to the login page.
     */
    public function cancel(Request $request): RedirectResponse
    {
        $request->session()->forget([
            'mfa.pending_user_id',
            'mfa.location',
            'mfa.remember',
            'mfa.redirect_url',
            'mfa.setup_verified',
        ]);

        return redirect()->route('login');
    }

    /**
     * Resolve the pending user from the session.
     */
    private function pendingUser(Request $request): User
    {
        $id = $request->session()->get('mfa.pending_user_id');

        $user = User::find($id);

        if (! $user instanceof User) {
            $request->session()->forget([
                'mfa.pending_user_id',
                'mfa.location',
                'mfa.remember',
                'mfa.redirect_url',
                'mfa.setup_verified',
            ]);

            throw new HttpResponseException(redirect()->route('login'));
        }

        return $user;
    }

    /**
     * Build a fallback location for the request when the session has none.
     *
     * @return array{country:string,state:string,state_code:string,provider:string}
     */
    private function locationFor(Request $request, User $user): array
    {
        $location = $request->session()->get('mfa.location');

        if (is_array($location) && isset($location['state_code'])) {
            return $location;
        }

        return \App\Services\GeolocationService::resolve($request->ip() ?? '0.0.0.0');
    }
}
