<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REDAS - Secure Access Gateway</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="icon" type="image/png" href="{{ asset('assets/images/nis.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/dashboard.css', 'resources/css/auth-pages.css', 'resources/css/styles.css'])
</head>

<body class="login-page modern-auth-page" data-page="login">
    <style>
        :root{
            --auth-bg-1:#0f172a;
            --auth-bg-2:#14532d;
            --auth-accent:#22c55e;
            --auth-accent-2:#16a34a;
            --auth-card:#ffffff;
            --auth-text:#0f172a;
            --auth-muted:#64748b;
            --auth-border:#e2e8f0;
        }

        .modern-auth-page{
            min-height:100vh;
            margin:0;
            display:flex;
            align-items:center;
            justify-content:center;
            background:
                radial-gradient(circle at 10% 10%, rgba(34,197,94,.18), transparent 35%),
                radial-gradient(circle at 90% 90%, rgba(16,185,129,.12), transparent 35%),
                linear-gradient(135deg, var(--auth-bg-1), var(--auth-bg-2));
            font-family: "Instrument Sans", system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            overflow-x:hidden;
        }

        .modern-auth-page .login-container{
            width:min(1100px, 94%);
            position:relative;
            animation:fadeInUp .8s ease both;
        }

        .modern-auth-page .login-card{
            border-radius:24px;
            overflow:hidden;
            border:1px solid rgba(255,255,255,.2);
            background:rgba(255,255,255,.12);
            backdrop-filter: blur(10px);
            box-shadow:0 20px 50px rgba(2,6,23,.35);
            display:grid;
            grid-template-columns: 1fr 1.05fr;
            min-height:640px;
        }

        .modern-auth-page .login-header{
            background:
                linear-gradient(160deg, rgba(15,23,42,.82), rgba(20,83,45,.88)),
                url('{{ asset('assets/images/nis-cover.jpeg') }}') center/cover no-repeat;
            color:#fff;
            padding:2.75rem 2.2rem;
            display:flex;
            flex-direction:column;
            justify-content:space-between;
            position:relative;
            isolation:isolate;
        }

        .modern-auth-page .login-header::after{
            content:'';
            position:absolute;
            inset:0;
            background:linear-gradient(180deg, rgba(15,23,42,.1), rgba(15,23,42,.5));
            z-index:-1;
        }

        .modern-auth-page .login-logo{
            width:72px;
            height:72px;
            object-fit:contain;
            background:#fff;
            border-radius:16px;
            padding:.55rem;
            box-shadow:0 8px 24px rgba(0,0,0,.24);
            animation:floatLogo 3s ease-in-out infinite;
        }

        .modern-auth-page .login-title{
            margin-top:1rem;
            font-size:2rem;
            line-height:1.1;
            font-weight:700;
            letter-spacing:.2px;
        }

        .modern-auth-page .login-subtitle{
            margin:.4rem 0 0;
            color:rgba(255,255,255,.86);
            font-size:1rem;
        }

        .brand-points{
            margin:1.5rem 0 0;
            padding:0;
            list-style:none;
            display:grid;
            gap:.75rem;
        }

        .brand-points li{
            display:flex;
            align-items:center;
            gap:.6rem;
            color:rgba(255,255,255,.92);
            font-size:.95rem;
        }

        .brand-points i{
            width:28px;
            height:28px;
            border-radius:50%;
            display:grid;
            place-items:center;
            background:rgba(255,255,255,.15);
            color:#bbf7d0;
            font-size:.8rem;
        }

        .modern-auth-page .login-body{
            background:var(--auth-card);
            padding:2.4rem 2rem 1.8rem;
            display:flex;
            flex-direction:column;
            justify-content:center;
        }

        .auth-welcome{
            margin-bottom:1.2rem;
        }

        .auth-welcome h2{
            font-size:1.35rem;
            color:var(--auth-text);
            margin:0;
            font-weight:700;
        }

        .auth-welcome p{
            margin:.3rem 0 0;
            color:var(--auth-muted);
            font-size:.95rem;
        }

        .modern-auth-page .alert{
            border-radius:12px;
            border:none;
            font-size:.92rem;
        }

        .modern-auth-page .form-floating{
            margin-bottom:1rem;
        }

        .modern-auth-page .form-control,
        .modern-auth-page .form-select{
            border-radius:12px;
            min-height:56px;
            border:1px solid var(--auth-border);
            box-shadow:none !important;
            transition:border-color .2s, box-shadow .2s, transform .2s;
        }

        .modern-auth-page .form-control:focus,
        .modern-auth-page .form-select:focus{
            border-color:var(--auth-accent);
            box-shadow:0 0 0 .18rem rgba(34,197,94,.18) !important;
            transform:translateY(-1px);
        }

        .modern-auth-page .form-floating > label{
            color:#475569;
        }

        .modern-auth-page .forgot-link{
            color:#0f766e;
            text-decoration:none;
            font-weight:600;
            font-size:.9rem;
        }

        .modern-auth-page .forgot-link:hover{
            text-decoration:underline;
        }

        .modern-auth-page .btn-login{
            border:none;
            border-radius:12px;
            min-height:52px;
            font-weight:700;
            letter-spacing:.2px;
            color:#fff;
            background:linear-gradient(135deg, var(--auth-accent), var(--auth-accent-2));
            box-shadow:0 8px 20px rgba(22,163,74,.34);
            transition:transform .2s ease, box-shadow .2s ease, filter .2s ease;
        }

        .modern-auth-page .btn-login:hover{
            transform:translateY(-2px);
            box-shadow:0 12px 24px rgba(22,163,74,.38);
            filter:brightness(1.03);
        }

        .modern-auth-page .btn-login:active{
            transform:translateY(0);
        }

        .modern-auth-page .divider{
            margin:1.2rem 0 .9rem;
            position:relative;
            text-align:center;
            color:#94a3b8;
            font-size:.86rem;
        }

        .modern-auth-page .divider::before{
            content:'';
            position:absolute;
            left:0;
            right:0;
            top:50%;
            border-top:1px solid #e2e8f0;
            transform:translateY(-50%);
        }

        .modern-auth-page .divider span{
            background:#fff;
            position:relative;
            padding:0 .8rem;
        }

        .modern-auth-page .register-link{
            text-align:center;
            margin-bottom:1rem;
            color:#475569;
            font-size:.95rem;
        }

        .modern-auth-page .register-link a{
            color:var(--auth-accent-2);
            font-weight:700;
            text-decoration:none;
        }

        .modern-auth-page .register-link a:hover{
            text-decoration:underline;
        }

        .modern-auth-page .footer-links{
            text-align:center;
            font-size:.86rem;
            color:#64748b;
        }

        .modern-auth-page .footer-links a{
            color:#64748b;
            text-decoration:none;
        }

        .modern-auth-page .footer-links a:hover{
            color:#0f766e;
        }

        @keyframes fadeInUp{
            from{opacity:0;transform:translateY(12px);}
            to{opacity:1;transform:translateY(0);}
        }

        @keyframes floatLogo{
            0%,100%{transform:translateY(0);}
            50%{transform:translateY(-4px);}
        }

        @media (max-width: 992px){
            .modern-auth-page .login-card{
                grid-template-columns:1fr;
                min-height:auto;
            }
            .modern-auth-page .login-header{
                min-height:260px;
            }
        }

        @media (max-width: 576px){
            .modern-auth-page .login-body{
                padding:1.5rem 1rem;
            }
            .modern-auth-page .login-title{
                font-size:1.65rem;
            }
        }
    </style>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div>
                    <img src="{{ asset('assets/images/nis.png') }}" alt="NIS Logo" class="login-logo">
                    <h1 class="login-title">NIS REDAS</h1>
                    <p class="login-subtitle">Secure Access Gateway</p>
                </div>

                <ul class="brand-points">
                    <li><i class="fas fa-shield-alt"></i> Role-based secure access</li>
                    <li><i class="fas fa-lock"></i> Protected authentication flow</li>
                    <li><i class="fas fa-chart-line"></i> Operational insights dashboard</li>
                </ul>
            </div>
            
            <div class="login-body">
                <div class="auth-welcome">
                    <h2>Welcome back</h2>
                    <p>Sign in to continue to your REDAS workspace.</p>
                </div>
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <span>{{ $errors->first('login') ?? $errors->first() }}</span>
                    </div>
                @endif

                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <form id="loginForm" method="POST" action="{{ route('login.submit') }}" novalidate>
                    @csrf
                    <!-- Role Selection -->
                    <div class="form-floating">
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                            <option value="" disabled {{ old('role') ? '' : 'selected' }}></option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                            <option value="zonal" {{ old('role') === 'zonal' ? 'selected' : '' }}>Zonal Commander</option>
                            <option value="state" {{ old('role') === 'state' ? 'selected' : '' }}>State Coordinator</option>
                            <option value="officer" {{ old('role') === 'officer' ? 'selected' : '' }}>Immigration Officer</option>
                        </select>
                        <label for="role"><i class="fas fa-user-shield me-2"></i>Select Role</label>
                    </div>

                    <!-- Service Number / Email Input -->
                    <div class="form-floating">
                        <input type="text" class="form-control @error('login') is-invalid @enderror" id="loginInput" name="login" placeholder="Service Number or Email" value="{{ old('login') }}" required>
                        <label for="loginInput"><i class="fas fa-id-card me-2"></i>Service Number / Email</label>
                    </div>

                    <!-- Password Input -->
                    <div class="form-floating">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" required>
                        <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rememberMe" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label text-muted" for="rememberMe">Remember me</label>
                        </div>
                        <a href="#" class="forgot-link" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">
                            <i class="fas fa-key me-1"></i>Forgot Password?
                        </a>
                    </div>

                    <!-- Login Button -->
                    <button type="submit" class="btn btn-login w-100" id="loginBtn">
                        <span class="btn-text"><i class="fas fa-sign-in-alt me-2"></i>Secure Login</span>
                    </button>
                </form>

                <div class="divider">
                    <span>or</span>
                </div>

                {{-- <p class="register-link">
                    Don't have an account? <a href="{{ route('register') }}">Register here</a>
                </p> --}}

                <div class="footer-links">
                    <a href="{{ url('/') }}"><i class="fas fa-home me-1"></i>Back to Home</a>
                    {{-- <span class="mx-2">|</span>
                    <a href="#"><i class="fas fa-shield-alt me-1"></i>Privacy Policy</a> --}}
                </div>
            </div>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--nis-green), var(--nis-green-light)); color: white;">
                    <h5 class="modal-title"><i class="fas fa-key me-2"></i>Reset Password</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="resetPasswordForm">
                        <div class="mb-3">
                            <label for="resetServiceNumber" class="form-label">Service Number</label>
                            <input type="text" class="form-control" id="resetServiceNumber" placeholder="NIS/XX/XXXX" required>
                        </div>
                        <div class="mb-3">
                            <label for="resetEmail" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="resetEmail" placeholder="officer@immigration.gov.ng" required>
                        </div>
                        <button type="submit" class="btn btn-login w-100" onclick="event.preventDefault(); alert('Password reset link has been sent to your email!'); $('#forgotPasswordModal').modal('hide');">
                            <i class="fas fa-paper-plane me-2"></i>Send Reset Link
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
