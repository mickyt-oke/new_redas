<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Recover access to your NIS-REDAS account using a backup code.">
    <title>NIS-REDAS | MFA Recovery</title>

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
        <div class="auth-brand animate-fade-up">
            <img src="{{ asset('assets/images/nis.png') }}" alt="NIS" class="auth-brand-logo">
            <div class="auth-brand-name">NIS&nbsp;REDAS</div>
            <div class="auth-brand-sub">Reporting Dashboard &amp; Archiving System</div>
            <div style="width:40px;height:3px;background:var(--gold-500);border-radius:2px;margin-top:12px;"></div>
        </div>

        <div class="auth-features animate-fade-up delay-2">
            @foreach([
                ['fas fa-life-ring',    'Recovery Code',       'Each code can only be used once. Keep them secure.'],
                ['fas fa-shield-alt',   'Account Security',    'This confirms you still have the backup codes saved during MFA setup.'],
                ['fas fa-user-lock',    'Lost Device?',        'After signing in, you can reset MFA from your profile if needed.'],
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

    <!-- ─── Right Panel: Recovery Form ─── -->
    <main class="auth-right">
        <div class="auth-right-inner animate-fade-up">

            <div style="margin-bottom:28px;">
                <h1 class="auth-welcome-title">Recover Your Account</h1>
                <p class="auth-welcome-sub">Enter one of your single-use backup codes for <strong>{{ $user->email }}</strong> to continue.</p>
            </div>

            @if($errors->any())
            <div style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;border-radius:var(--radius-md);padding:12px 16px;font-size:.875rem;margin-bottom:20px;display:flex;gap:10px;align-items:flex-start;">
                <i class="fas fa-exclamation-circle" style="margin-top:2px;flex-shrink:0;"></i>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            @if(session('status'))
            <div style="background:#dcfce7;border:1px solid #86efac;color:#15803d;border-radius:var(--radius-md);padding:12px 16px;font-size:.875rem;margin-bottom:20px;display:flex;gap:10px;align-items:flex-start;">
                <i class="fas fa-check-circle" style="margin-top:2px;flex-shrink:0;"></i>
                <span>{{ session('status') }}</span>
            </div>
            @endif

            <form method="POST" action="{{ route('mfa.recovery.verify') }}" novalidate>
                @csrf

                <div class="auth-form-group">
                    <label class="form-label-nis" for="recoveryCode">
                        <i class="fas fa-life-ring me-1 text-nis"></i> Backup Recovery Code
                    </label>
                    <div class="auth-input-wrap">
                        <input type="text"
                            class="auth-input @error('code') is-invalid @enderror"
                            id="recoveryCode" name="code"
                            placeholder="XXXXXXXX"
                            autocomplete="off"
                            maxlength="16"
                            required
                            autofocus>
                        <span class="auth-input-icon"><i class="fas fa-key"></i></span>
                    </div>
                    @error('code')
                    <div style="color:var(--color-danger);font-size:.76rem;margin-top:4px;"><i class="fas fa-circle-xmark me-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-auth" style="margin-bottom:12px;">
                    <i class="fas fa-sign-in-alt"></i>
                    <span class="btn-text">Verify &amp; Continue</span>
                </button>
            </form>

            <form method="POST" action="{{ route('mfa.cancel') }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn-auth" style="background:transparent;color:var(--gray-500);border:1px solid var(--gray-300);">
                    <i class="fas fa-times"></i>
                    <span class="btn-text">Cancel</span>
                </button>
            </form>

            <div style="margin-top:20px;text-align:center;font-size:.875rem;">
                <a href="{{ route('mfa.challenge') }}" style="color:var(--nis-600);text-decoration:none;">
                    <i class="fas fa-arrow-left me-1"></i> Back to MFA code
                </a>
            </div>

        </div>
    </main>
</div>

</body>
</html>
