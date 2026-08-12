<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Secure login portal for NIS-REDAS — Nigeria Immigration Service Reporting Dashboard & Archiving System.">
    <title>NIS-REDAS | Secure Login</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/nis.png') }}">
    @include('partials.head-meta')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @laravelPWA

    <style>
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-8px); }
            40% { transform: translateX(8px); }
            60% { transform: translateX(-5px); }
            80% { transform: translateX(5px); }
        }

        .shake {
            animation: shake .5s ease;
        }

        .login-page {
            min-height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
        }

        .login-card {
            width: 100%;
            max-width: 460px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
            padding: 30px 24px;
        }

        .login-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 18px;
        }

        .login-brand img {
            width: 38px;
            height: 38px;
            object-fit: contain;
        }

        .login-brand-name {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--nis-700);
            letter-spacing: .02em;
        }

        .login-title {
            margin: 0;
            text-align: center;
            font-size: 1.7rem;
            line-height: 1.15;
            font-weight: 800;
            color: #0f172a;
        }

        .login-subtitle {
            margin: 8px 0 24px;
            text-align: center;
            color: #475569;
            font-size: .94rem;
        }

        .auth-form-group {
            margin-bottom: 16px;
        }

        .pw-wrap {
            position: relative;
        }

        .pw-wrap .pw-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--gray-400);
            padding: 4px;
        }

        .pw-wrap .pw-toggle:hover {
            color: var(--gray-600);
        }

        .pw-wrap input {
            padding-right: 40px;
        }

        .inline-error {
            color: var(--color-danger);
            font-size: .78rem;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .helper-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 20px;
        }

        .forgot-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: .84rem;
            color: var(--nis-600);
            font-weight: 600;
            padding: 0;
        }

        .back-link-wrap {
            margin-top: 18px;
            text-align: center;
        }

        @media (max-width: 576px) {
            .login-page {
                padding: 16px;
            }

            .login-card {
                border-radius: 14px;
                padding: 24px 16px;
            }

            .login-title {
                font-size: 1.45rem;
            }
        }
    </style>
</head>
<body class="auth-page">
@include('partials.preloader')

<div class="login-page">
    <main class="login-card {{ $errors->any() ? 'shake' : '' }}">
        <div class="login-brand">
            <img src="{{ asset('assets/images/nis.png') }}" alt="NIS">
            <div class="login-brand-name">NIS REDAS</div>
        </div>

        <h1 class="login-title">Welcome Back</h1>
        <p class="login-subtitle">Sign in to access your dashboard.</p>

        @if($errors->any())
        <div style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;border-radius:var(--radius-md);padding:12px 16px;font-size:.875rem;margin-bottom:20px;display:flex;gap:10px;align-items:flex-start;">
            <i class="fas fa-exclamation-circle" style="margin-top:2px;flex-shrink:0;"></i>
            <span>{{ $errors->first('username') ?? $errors->first('password') ?? $errors->first() }}</span>
        </div>
        @endif

        @if(session('status'))
        <div style="background:#dcfce7;border:1px solid #86efac;color:#15803d;border-radius:var(--radius-md);padding:12px 16px;font-size:.875rem;margin-bottom:20px;display:flex;gap:10px;align-items:flex-start;">
            <i class="fas fa-check-circle" style="margin-top:2px;flex-shrink:0;"></i>
            <span>{{ session('status') }}</span>
        </div>
        @endif

        <form id="loginForm" method="POST" action="{{ route('login.submit') }}" novalidate>
            @csrf

            <div class="auth-form-group">
                <label class="form-label-nis" for="username">
                    <i class="fas fa-user me-1 text-nis"></i> Username
                </label>
                <div class="auth-input-wrap">
                    <input type="text"
                        class="auth-input @error('username') is-invalid @enderror"
                        id="username"
                        name="username"
                        placeholder="Enter email or service number"
                        value="{{ old('username') }}"
                        autocomplete="username"
                        required>
                    <span class="auth-input-icon"><i class="fas fa-id-badge"></i></span>
                </div>
                @error('username')
                <div class="inline-error"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>
                @enderror
            </div>

            <div class="auth-form-group">
                <label class="form-label-nis" for="password">
                    <i class="fas fa-lock me-1 text-nis"></i> Password
                </label>
                <div class="auth-input-wrap pw-wrap">
                    <input type="password"
                        class="auth-input @error('password') is-invalid @enderror"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        autocomplete="current-password"
                        required>
                    <span class="auth-input-icon"><i class="fas fa-lock"></i></span>
                    <button type="button" class="pw-toggle" aria-label="Toggle password visibility">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password')
                <div class="inline-error"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>
                @enderror
            </div>

            <div class="helper-row">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:.84rem;color:var(--gray-600);">
                    <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} style="accent-color:var(--nis-600);width:15px;height:15px;">
                    Keep me signed in
                </label>

                <a href="{{ route('password.forgot.form') }}" class="forgot-btn" style="text-decoration:none;">
                    Forgot Password?
                </a>
            </div>

            <button type="submit" class="btn-auth" id="loginBtn">
                <i class="fas fa-sign-in-alt"></i>
                <span class="btn-text">Secure Sign In</span>
            </button>
        </form>

        <div class="back-link-wrap">
            <a href="{{ url('/') }}" class="auth-back-link">
                <i class="fas fa-arrow-left"></i> Back to Home
            </a>
        </div>
    </main>
</div>

{{-- <div class="modal fade" id="forgotModal" aria-labelledby="forgotModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:var(--radius-lg);border:none;overflow:hidden;">
            <div class="modal-header" style="background:var(--nis-700);color:white;border:none;padding:16px 20px;">
                <h5 class="modal-title" id="forgotModalLabel" style="font-weight:700;font-size:1rem;">
                    <i class="fas fa-key me-2"></i>Reset Your Password
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <p style="font-size:.875rem;color:var(--gray-500);margin-bottom:20px;">
                    Enter your service number and registered email address. We'll send a password reset link to your email.
                </p>
                <form id="forgotForm">
                    <div class="auth-form-group">
                        <label class="form-label-nis">Service Number</label>
                        <div class="auth-input-wrap">
                            <input type="text" class="auth-input" placeholder="NIS/HQ/2023/1234" required>
                            <span class="auth-input-icon"><i class="fas fa-id-card"></i></span>
                        </div>
                    </div>
                    <div class="auth-form-group">
                        <label class="form-label-nis">Email Address</label>
                        <div class="auth-input-wrap">
                            <input type="email" class="auth-input" placeholder="officer@immigration.gov.ng" required>
                            <span class="auth-input-icon"><i class="fas fa-envelope"></i></span>
                        </div>
                    </div>
                    <button type="button" class="btn-nis btn-primary-nis full-width"
                        onclick="REDAS.showToast('Password reset link sent to your email.','success');bootstrap.Modal.getInstance(document.getElementById('forgotModal')).hide();">
                        <i class="fas fa-paper-plane"></i> Send Reset Link
                    </button>
                </form>
            </div>
        </div>
    </div>
</div> --}}
</body>
</html>
