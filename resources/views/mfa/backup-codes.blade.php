<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Save your MFA backup codes for NIS-REDAS.">
    <title>NIS-REDAS | Backup Codes</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/nis.png') }}">
    @include('partials.head-meta')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @laravelPWA

    <style>
        .backup-code {
            font-family: monospace;
            font-size: 1rem;
            letter-spacing: 0.05em;
            background: var(--gray-50);
            border: 1px dashed var(--gray-300);
            border-radius: var(--radius-md);
            padding: 10px 14px;
            text-align: center;
        }
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
                ['fas fa-save',      'Save These Codes',      'Store them in a password manager or print them. You will not see them again.'],
                ['fas fa-lock',      'Single Use Only',     'Each code can be used once if you lose access to your authenticator app.'],
                ['fas fa-shield-alt','Do Not Share',        'Backup codes grant account access — keep them confidential.'],
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
    </aside>

    <!-- ─── Right Panel: Backup Codes ─── -->
    <main class="auth-right">
        <div class="auth-right-inner animate-fade-up">

            <div style="margin-bottom:24px;">
                <h1 class="auth-welcome-title">MFA Enabled Successfully</h1>
                <p class="auth-welcome-sub">Multi-factor authentication is now active on your account. Save your backup codes before continuing.</p>
            </div>

            <div style="background:#dcfce7;border:1px solid #86efac;color:#15803d;border-radius:var(--radius-md);padding:12px 16px;font-size:.875rem;margin-bottom:24px;display:flex;gap:10px;align-items:flex-start;">
                <i class="fas fa-check-circle" style="margin-top:2px;flex-shrink:0;"></i>
                <span>Your authenticator was verified. These codes are the only way to recover access if your device is lost.</span>
            </div>

            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:28px;">
                @foreach($codes as $index => $code)
                <div class="backup-code">{{ $code }}</div>
                @endforeach
            </div>

            <form method="POST" action="{{ route('mfa.complete') }}" novalidate>
                @csrf

                <button type="submit" class="btn-auth" style="margin-bottom:12px;">
                    <i class="fas fa-arrow-right"></i>
                    <span class="btn-text">Continue to Dashboard</span>
                </button>
            </form>

            <form method="POST" action="{{ route('mfa.cancel') }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn-auth" style="background:transparent;color:var(--gray-500);border:1px solid var(--gray-300);">
                    <i class="fas fa-sign-in-alt"></i>
                    <span class="btn-text">Return to Login</span>
                </button>
            </form>

        </div>
    </main>
</div>

</body>
</html>
