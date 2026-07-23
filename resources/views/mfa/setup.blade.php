<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Set up multi-factor authentication for NIS-REDAS.">
    <title>NIS-REDAS | Set Up MFA</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/nis.png') }}">
    @include('partials.head-meta')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @laravelPWA

    <style>
        .mfa-deco-circle { position: absolute; border-radius: 50%; pointer-events: none; }
    </style>
</head>
<body class="auth-page">

@include('partials.preloader')

<div class="auth-split">

    <!-- ─── Left Panel: Branding ─── -->
    <aside class="auth-left">
        <div class="auth-deco-circle" style="width:280px;height:280px;bottom:-80px;left:-80px;background:rgba(197,146,42,0.06);"></div>
        <div class="auth-deco-circle" style="width:160px;height:160px;top:60px;right:-40px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.06);"></div>

        <div class="auth-brand animate-fade-up">
            <img src="{{ asset('assets/images/nis.png') }}" alt="NIS" class="auth-brand-logo">
            <div class="auth-brand-name">NIS&nbsp;REDAS</div>
            <div class="auth-brand-sub">Reporting Dashboard &amp; Archiving System</div>
            <div style="width:40px;height:3px;background:var(--gold-500);border-radius:2px;margin-top:12px;"></div>
        </div>

        <div class="auth-features animate-fade-up delay-2">
            @foreach([
                ['fas fa-shield-alt',   'Enhanced Security',   'MFA protects your account even if your password is compromised.'],
                ['fas fa-mobile-alt',   'Authenticator App',   'Use Google Authenticator, Authy, or any TOTP-compatible app.'],
                ['fas fa-key',          'Backup Codes',        'Single-use backup codes keep you covered when your phone is unavailable.'],
            ] as [$icon, $title, $desc])
            <div class="auth-feature-item">
                <div class="auth-feature-icon"><i class="{{ $icon }}"></i></div>
                <div class="auth-feature-text">
                    <div class="auth-feature-title">{{ $title }}</div>
                    <div class="auth-feature-desc">{{ $desc }}</div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="auth-footer-text">
            <i class="fas fa-lock me-1"></i>
            RESTRICTED — For Internal Use Only<br>
            &copy; {{ date('Y') }}
        </div>
    </aside>

    <!-- ─── Right Panel: Setup Form ─── -->
    <main class="auth-right">
        <div class="auth-right-inner animate-fade-up">

            <div style="margin-bottom:28px;">
                <h1 class="auth-welcome-title">Set Up Multi-Factor Authentication</h1>
                <p class="auth-welcome-sub">Scan the QR code with your authenticator app, then enter the 6-digit code to confirm.</p>
            </div>

            @if($errors->any())
            <div style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;border-radius:var(--radius-md);padding:12px 16px;font-size:.875rem;margin-bottom:20px;display:flex;gap:10px;align-items:flex-start;">
                <i class="fas fa-exclamation-circle" style="margin-top:2px;flex-shrink:0;"></i>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            <div style="display:flex;flex-direction:column;align-items:center;gap:20px;margin-bottom:28px;">
                <div style="background:white;border:1px solid var(--gray-200);border-radius:var(--radius-lg);padding:16px;box-shadow:var(--shadow-sm);">
                    {!! $qrSvg !!}
                </div>

                <div style="text-align:center;width:100%;">
                    <label style="font-size:.72rem;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.06em;">Secret Key</label>
                    <div style="font-family:monospace;font-size:.9rem;background:var(--gray-50);border:1px solid var(--gray-200);border-radius:var(--radius-md);padding:10px 12px;word-break:break-all;margin-top:6px;user-select:all;">
                        {{ chunk_split($secret, 4, ' ') }}
                    </div>
                    <p style="font-size:.75rem;color:var(--gray-400);margin-top:8px;">
                        If you cannot scan the QR code, enter the secret key manually into your authenticator app.
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('mfa.setup.verify') }}" novalidate>
                @csrf

                <div class="auth-form-group">
                    <label class="form-label-nis" for="mfaCode">
                        <i class="fas fa-shield-alt me-1 text-nis"></i> 6-Digit Authentication Code
                    </label>
                    <div class="auth-input-wrap">
                        <input type="text"
                            class="auth-input @error('code') is-invalid @enderror"
                            id="mfaCode" name="code"
                            placeholder="000000"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            maxlength="6"
                            autocomplete="one-time-code"
                            required>
                        <span class="auth-input-icon"><i class="fas fa-key"></i></span>
                    </div>
                    @error('code')
                    <div style="color:var(--color-danger);font-size:.76rem;margin-top:4px;"><i class="fas fa-circle-xmark me-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-auth" style="margin-bottom:12px;">
                    <i class="fas fa-check-circle"></i>
                    <span class="btn-text">Verify &amp; Enable MFA</span>
                </button>
            </form>

            <form method="POST" action="{{ route('mfa.cancel') }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn-auth" style="background:transparent;color:var(--gray-500);border:1px solid var(--gray-300);">
                    <i class="fas fa-times"></i>
                    <span class="btn-text">Cancel</span>
                </button>
            </form>

        </div>
    </main>
</div>

</body>
</html>
