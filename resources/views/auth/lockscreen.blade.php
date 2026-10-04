<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NIS-REDAS | Session Locked</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/nis.png') }}">
    @include('partials.head-meta')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .lockscreen-page {
            min-height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
        }
        .lockscreen-card {
            width: 100%;
            max-width: 400px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
            padding: 40px 24px;
            text-align: center;
        }
        .lockscreen-brand {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .lockscreen-brand img {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }
        .lockscreen-title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 800;
            color: #0f172a;
        }
        .lockscreen-subtitle {
            margin: 8px 0 24px;
            color: #475569;
            font-size: .9rem;
        }
        .auth-form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .inline-error {
            color: var(--color-danger);
            font-size: .78rem;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
    </style>
</head>
<body class="auth-page">
    @include('partials.preloader')
    <div class="lockscreen-page">
        <main class="lockscreen-card {{ $errors->any() ? 'shake' : '' }}">
            <div class="lockscreen-brand">
                <img src="{{ asset('assets/images/nis.png') }}" alt="NIS">
            </div>
            <h1 class="lockscreen-title">Session Locked</h1>
            <p class="lockscreen-subtitle">Please enter your passcode to unlock your session</p>

            @if($errors->any())
            <div style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;border-radius:var(--radius-md);padding:12px 16px;font-size:.875rem;margin-bottom:20px;display:flex;gap:10px;align-items:flex-start;text-align:left;">
                <i class="fas fa-exclamation-circle" style="margin-top:2px;flex-shrink:0;"></i>
                <span>{{ $errors->first('passcode') }}</span>
            </div>
            @endif

            <form method="POST" action="{{ route('lockscreen.unlock') }}">
                @csrf
                <div class="auth-form-group">
                    <label class="form-label-nis" for="passcode">
                        <i class="fas fa-lock me-1 text-nis"></i> Passcode
                    </label>
                    <div class="auth-input-wrap">
                        <input type="password" 
                               class="auth-input @error('passcode') is-invalid @enderror" 
                               id="passcode" 
                               name="passcode" 
                               placeholder="Enter lockscreen passcode" 
                               required 
                               autofocus>
                        <span class="auth-input-icon"><i class="fas fa-key"></i></span>
                    </div>
                    @error('passcode')
                    <div class="inline-error"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-auth" style="width: 100%;">
                    <i class="fas fa-unlock-alt"></i>
                    <span class="btn-text">Unlock Session</span>
                </button>
            </form>
        </main>
    </div>
</body>
</html>
