<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password</title>
    @include('partials.head-meta')
    @laravelPWA
    <link rel="stylesheet" href="/assets/app.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        .forgot-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .forgot-card {
            width: 100%;
            max-width: 560px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
            padding: 32px;
        }
        .forgot-title {
            margin: 0 0 8px;
            font-size: 1.75rem;
            font-weight: 800;
            color: #0f172a;
        }
        .forgot-subtitle {
            margin: 0 0 24px;
            color: #475569;
            font-size: .95rem;
            line-height: 1.5;
        }
        .forgot-input-group {
            margin-bottom: 18px;
        }
        .forgot-input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #334155;
        }
        .forgot-input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            font-size: 1rem;
            color: #0f172a;
        }
        .forgot-btn-primary {
            width: 100%;
            padding: 14px 16px;
            border: none;
            border-radius: 12px;
            background: #1d4ed8;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }
        .forgot-back-link {
            margin-top: 16px;
            display: block;
            text-align: center;
            color: #1d4ed8;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body class="forgot-page">

@include('partials.preloader')

<div class="forgot-card">
    <div class="page-header">
        <div>
            <h1 class="page-title">Forgot Password</h1>
            <p class="page-subtitle">Enter your registered email address to receive a password reset link.</p>
        </div>
    </div>

    @if(session('status'))
        <div style="background:#dcfce7;border:1px solid #86efac;padding:12px 14px;border-radius:12px;margin-bottom:20px;color:#166534;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.forgot') }}" style="background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:24px;">
        @csrf

        <div style="margin-bottom:18px;">
            <label style="display:block;font-weight:600;margin-bottom:8px;color:#334155;">Email Address</label>
            <input type="email" name="email" value="{{ old('email') }}" style="width:100%;padding:12px 14px;border:1px solid #cbd5e1;border-radius:12px;" placeholder="officer@immigration.gov.ng" required>
            @error('email')
                <div style="color:#b91c1c;font-size:.84rem;margin-top:8px;">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" style="width:100%;padding:12px 16px;border:none;border-radius:12px;background:#1d4ed8;color:#fff;font-weight:700;cursor:pointer;">Send reset link</button>

        <div style="margin-top:16px;text-align:center;">
            <a href="{{ route('login') }}" style="color:#1d4ed8;text-decoration:none;font-weight:600;">Back to sign in</a>
        </div>
    </form>
</div>
</body>
</html>
