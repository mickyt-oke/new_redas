<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Password recovery for NIS-REDAS — Nigeria Immigration Service Reporting Dashboard & Archiving System.">
    <title>NIS-REDAS | Forgot Password</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/nis.png') }}">
    @include('partials.head-meta')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @laravelPWA
    <style>
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
        .page-header {
            margin-bottom: 24px;
            text-align: center;
        }
        .forgot-title {
            margin: 0 0 8px;
            font-size: 1.75rem;
            font-weight: 800;
            color: #0a520a;
        }
        .forgot-subtitle {
            margin: 0 0 24px;
            color: #475569;
            font-size: .95rem;
            line-height: 1.5;
        }
        .login-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 18px;

        }
        .login-brand img {
            height: 50px;
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
            background: #0a520a;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }
        .forgot-back-link {
            margin-top: 16px;
            display: block;
            text-align: center;
            color: #0a520a;
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
            <div class="login-brand">
            <img src="{{ asset('assets/images/nis.png') }}" alt="NIS">
        </div>
            <h1 class="page-title">Forgot Password</h1>
            <p class="page-subtitle">Enter your registered email address to receive a password reset link.</p>
        </div>
    </div>

    @if(session('status'))
        <div style="background:#dcfce7;border:1px solid #86efac;padding:12px 14px;border-radius:12px;margin-bottom:20px;color:#166534;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.forgot') }}">
        @csrf

         <div class="auth-form-group">
            <label class="forgot-label">Email Address</label>
            <input type="email" class="auth-input @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="officer@immigration.gov.ng" required>
            @error('email')
                <div style="color:#b91c1c;font-size:.84rem;margin-top:8px;">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" style="width:100%;padding:12px 16px;border:none;border-radius:12px;background:#0a520a;color:#fff;font-weight:700;cursor:pointer;">Send reset link</button>

        <div style="margin-top:16px;text-align:center;">
            <a href="{{ route('login') }}" class="forgot-back-link">Back to sign in</a>
        </div>
    </form>
</div>
</body>
</html>
