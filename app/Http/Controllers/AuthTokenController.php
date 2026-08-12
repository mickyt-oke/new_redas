<?php

namespace App\Http\Controllers;

use App\Models\AuthToken;
use App\Models\User;
use App\Services\Messaging\ResendMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthTokenController extends Controller
{
    private function createToken(User $user, string $type, int $ttlSeconds): array
    {
        $rawToken = Str::random(64);
        $tokenHash = hash('sha256', $rawToken);

        $token = AuthToken::create([
            'user_id' => $user->id,
            'type' => $type,
            'token_hash' => $tokenHash,
            'expires_at' => now()->addSeconds($ttlSeconds),
            'used_at' => null,
        ]);

        return [
            'auth_token' => $token,
            'raw_token' => $rawToken,
        ];
    }

    private function consumeTokenOrFail(string $rawToken, string $type): AuthToken
    {
        $validator = Validator::make(['token' => $rawToken], [
            'token' => ['required', 'string', 'min:16'],
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages(['token' => ['Invalid token.']]);
        }

        $tokenHash = hash('sha256', $rawToken);

        /** @var AuthToken|null $token */
        $token = AuthToken::query()
            ->where('type', $type)
            ->where('token_hash', $tokenHash)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->orderByDesc('created_at')
            ->first();

        if (! $token) {
            throw ValidationException::withMessages(['token' => ['Token invalid or expired.']]);
        }

        $token->used_at = now();
        $token->save();

        return $token;
    }

    private function sendMagicLink(string $templateKey, array $variables, string $toEmail, ?string $toName = null): void
    {
        $mailer = new ResendMailService();
        $mailer->sendFromTemplate(
            $templateKey,
            $variables,
            $toEmail,
            $toName
        );
    }

    /**
     * GET /verify-email/{token}
     */
    public function verifyEmail(string $token)
    {
        $ttlSeconds = (int) env('AUTH_TOKEN_TTL_SECONDS', 3600); // 1 hour default (used only for request endpoints)

        $authToken = $this->consumeTokenOrFail($token, 'email_verify');

        $user = User::query()->findOrFail($authToken->user_id);
        $user->email_verified_at = now();
        $user->save();

        return redirect()
            ->route('login')
            ->with('status', 'Email verified successfully.');
    }

    /**
     * POST /password/forgot
     * (If you later add UI, wire it to this endpoint)
     */
    public function requestPasswordReset(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        /** @var User|null $user */
        $user = User::query()->where('email', $validated['email'])->first();

        // Avoid user enumeration: always return 200
        if ($user) {
            $ttlSeconds = (int) env('AUTH_TOKEN_TTL_SECONDS', 3600);

            $created = $this->createToken($user, 'password_reset', $ttlSeconds);
            $rawToken = (string) $created['raw_token'];

            $frontendUrl = (string) env('APP_URL', 'http://localhost');
            $magicLink = rtrim($frontendUrl, '/').'/password/reset/'.$rawToken;

            $this->sendMagicLink(
                'password_reset_magic_link',
                [
                    'name' => $user->name,
                    'magic_link' => $magicLink,
                    'expires_in_minutes' => (int) ceil($ttlSeconds / 60),
                ],
                $user->email,
                $user->name
            );
        }

        return redirect()->route('login')->with('status', 'If the email exists, a reset link has been sent.');
    }

    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * GET /password/reset/{token}
     */
    public function showResetForm(string $token)
    {
        // Minimal: redirect with status if invalid token
        try {
            // Consume later on POST; here we only validate it exists/valid by running consume but then can't reuse.
            // Instead, we "probe" by checking without consuming.
            $tokenHash = hash('sha256', $token);

            $authToken = AuthToken::query()
                ->where('type', 'password_reset')
                ->where('token_hash', $tokenHash)
                ->whereNull('used_at')
                ->where('expires_at', '>', now())
                ->orderByDesc('created_at')
                ->first();

            if (! $authToken) {
                throw ValidationException::withMessages(['token' => ['Token invalid or expired.']]);
            }

            return view('password-reset', ['token' => $token]);
        } catch (ValidationException $e) {
            return redirect()->route('login')->withErrors(['token' => $e->errors()['token'][0] ?? 'Invalid token.']);
        }
    }

    /**
     * POST /password/reset/{token}
     */
    public function resetPassword(Request $request, string $token)
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $authToken = $this->consumeTokenOrFail($token, 'password_reset');
        $user = User::query()->findOrFail($authToken->user_id);

        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()->route('login')->with('status', 'Password reset successfully.');
    }
}
