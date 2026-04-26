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
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Shake animation for error state */
        @keyframes shake {
            0%,100%{transform:translateX(0)} 20%{transform:translateX(-8px)} 40%{transform:translateX(8px)} 60%{transform:translateX(-5px)} 80%{transform:translateX(5px)}
        }
        .shake { animation: shake .5s ease; }

        /* Selected role label */
        .auth-role-option:checked + .auth-role-label {
            border-color: var(--nis-600);
            color: var(--nis-700);
            background: var(--nis-50);
            font-weight: 700;
        }

        /* Password wrapper */
        .pw-wrap { position: relative; }
        .pw-wrap .pw-toggle {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer; color: var(--gray-400); padding: 4px;
        }
        .pw-wrap .pw-toggle:hover { color: var(--gray-600); }
        .pw-wrap input { padding-right: 40px; }

        /* Animated left panel decoration */
        .auth-deco-circle {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }
    </style>
</head>
<body class="auth-page">

<div class="auth-split">

    <!-- ─── Left Panel: Branding ─── -->
    <aside class="auth-left">
        <!-- Decorative circles -->
        <div class="auth-deco-circle" style="width:280px;height:280px;bottom:-80px;left:-80px;background:rgba(197,146,42,0.06);"></div>
        <div class="auth-deco-circle" style="width:160px;height:160px;top:60px;right:-40px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.06);"></div>

        <!-- Brand -->
        <div class="auth-brand animate-fade-up">
            <img src="{{ asset('assets/images/nis.png') }}" alt="NIS" class="auth-brand-logo">
            <div class="auth-brand-name">NIS&nbsp;REDAS</div>
            <div class="auth-brand-sub">Reporting Dashboard &amp; Archiving System</div>
            <div style="width:40px;height:3px;background:var(--gold-500);border-radius:2px;margin-top:12px;"></div>
        </div>

        <!-- Features -->
        <div class="auth-features animate-fade-up delay-2">
            @foreach([
                ['fas fa-layer-group',    'Role-Based Access Control',   'Dashboards and data access tailored to Administrative and Operational Records.'],
                ['fas fa-shield-alt',     'Security Compliance',         'All data encrypted at rest and in transit. Full audit trail.'],
                ['fas fa-chart-line',     'Real-Time Analytics',         'Live KPI dashboards, formation compliance heatmaps, and trend reports.'],
                ['fas fa-archive',        'Digital Archive',             'Centralised, searchable repository for all NIS operational records.'],
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

        <!-- Footer note -->
        <div class="auth-footer-text">
            <i class="fas fa-lock me-1"></i>
            RESTRICTED — For Internal Use Only<br>
           &copy; {{ date('Y') }}
        </div>
    </aside>

    <!-- ─── Right Panel: Form ─── -->
    <main class="auth-right">
        <div class="auth-right-inner animate-fade-up">

            <!-- Header -->
            <div style="margin-bottom:32px;">
                {{-- <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                    <img src="{{ asset('assets/images/nis.png') }}" alt="NIS" style="height:36px;" class="d-md-none">
                    <span class="d-md-none" style="font-size:1rem;font-weight:800;color:var(--nis-600);">NIS REDAS</span>
                </div> --}}
                <h1 class="auth-welcome-title">Welcome Back</h1>
                <p class="auth-welcome-sub">Sign in with your NIS service number to access your dashboard.</p>
            </div>

            <!-- Error / Status Alerts -->
            @if($errors->any())
            <div style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;border-radius:var(--radius-md);padding:12px 16px;font-size:.875rem;margin-bottom:20px;display:flex;gap:10px;align-items:flex-start;">
                <i class="fas fa-exclamation-circle" style="margin-top:2px;flex-shrink:0;"></i>
                <span>{{ $errors->first('login') ?? $errors->first('role') ?? $errors->first() }}</span>
            </div>
            @endif

            @if(session('status'))
            <div style="background:#dcfce7;border:1px solid #86efac;color:#15803d;border-radius:var(--radius-md);padding:12px 16px;font-size:.875rem;margin-bottom:20px;display:flex;gap:10px;align-items:flex-start;">
                <i class="fas fa-check-circle" style="margin-top:2px;flex-shrink:0;"></i>
                <span>{{ session('status') }}</span>
            </div>
            @endif

            <!-- Role Selection -->
            <div style="margin-bottom:20px;">
                <label style="font-size:.78rem;font-weight:700;color:var(--gray-600);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:10px;">
                    Select Your Access Level
                </label>
                <div class="auth-role-grid">
                    @foreach([
                        ['officer', 'fas fa-user-edit',    'Data Entry Officer'],
                        ['state',   'fas fa-tasks',         'State Supervisor'],
                        ['zonal',   'fas fa-sitemap',       'Zonal Commander'],
                        ['admin',   'fas fa-user-shield',   'Administrator'],
                    ] as [$val, $icon, $label])
                    <input type="radio" name="role" id="role_{{ $val }}" class="auth-role-option" value="{{ $val }}" {{ old('role') === $val ? 'checked' : '' }} required>
                    <label for="role_{{ $val }}" class="auth-role-label">
                        <span class="auth-role-icon"><i class="{{ $icon }}"></i></span>
                        {{ $label }}
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- Login Form -->
            <form id="loginForm" method="POST" action="{{ route('login.submit') }}" novalidate>
                @csrf

                <!-- Hidden role field (synced from radio) -->
                <input type="hidden" name="role" id="roleHidden" value="{{ old('role', '') }}">

                <!-- Service Number / Email -->
                <div class="auth-form-group">
                    <label class="form-label-nis" for="loginInput">
                        <i class="fas fa-id-card me-1 text-nis"></i> Service Number or Email
                    </label>
                    <div class="auth-input-wrap">
                        <input type="text"
                            class="auth-input @error('login') is-invalid @enderror"
                            id="loginInput" name="login"
                            placeholder="e.g. NIS/HQ/2023/1234 or officer@immigration.gov.ng"
                            value="{{ old('login') }}"
                            autocomplete="username"
                            required>
                        <span class="auth-input-icon"><i class="fas fa-id-badge"></i></span>
                    </div>
                    @error('login')
                    <div style="color:var(--color-danger);font-size:.76rem;margin-top:4px;"><i class="fas fa-circle-xmark me-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="auth-form-group">
                    <label class="form-label-nis" for="password">
                        <i class="fas fa-lock me-1 text-nis"></i> Password
                    </label>
                    <div class="auth-input-wrap pw-wrap">
                        <input type="password"
                            class="auth-input @error('password') is-invalid @enderror"
                            id="password" name="password"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required>
                        <span class="auth-input-icon"><i class="fas fa-lock"></i></span>
                        <button type="button" class="pw-toggle" aria-label="Toggle password visibility">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                    <div style="color:var(--color-danger);font-size:.76rem;margin-top:4px;"><i class="fas fa-circle-xmark me-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remember + Forgot -->
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:.84rem;color:var(--gray-600);">
                        <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}
                            style="accent-color:var(--nis-600);width:15px;height:15px;">
                        Keep me signed in
                    </label>
                    <button type="button" data-bs-toggle="modal" data-bs-target="#forgotModal"
                        style="background:none;border:none;cursor:pointer;font-size:.84rem;color:var(--nis-600);font-weight:600;padding:0;">
                        Forgot Password?
                    </button>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-auth" id="loginBtn">
                    <i class="fas fa-sign-in-alt"></i>
                    <span class="btn-text">Secure Sign In</span>
                </button>
            </form>

            <a href="{{ url('/') }}" class="auth-back-link">
                <i class="fas fa-arrow-left"></i> Back to Home
            </a>
        </div>
    </main>
</div>

<!-- ─── Forgot Password Modal ─── -->
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

<script>
    /* Sync hidden role field from radio buttons */
    document.querySelectorAll('.auth-role-option').forEach(opt => {
        opt.addEventListener('change', () => {
            document.getElementById('roleHidden').value = opt.value;
        });
    });

    /* Pre-fill role hidden if page reloaded */
    const checkedRole = document.querySelector('.auth-role-option:checked');
    if (checkedRole) document.getElementById('roleHidden').value = checkedRole.value;

    /* Shake on error */
    @if($errors->any())
    document.querySelector('.auth-right-inner')?.classList.add('shake');
    @endif
</script>
</body>
</html>
