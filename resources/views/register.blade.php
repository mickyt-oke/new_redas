{{--
| NIS-REDAS Registration Page
| Maintainer: GreatMindsTech
| Last updated: {{ date('Y-m-d') }}
|
| - Follows backend validation rules
| - Accessible, clear error feedback
| - Prevents double submit
| - All fields required
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Secure registration portal for NIS-REDAS — Nigeria Immigration Service Reporting Dashboard & Archiving System.">
    <title>NIS-REDAS | Secure Registration</title>

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

        .password-strength {
            min-height: 18px;
            font-size: .875rem;
            margin: .35rem 0 .2rem;
        }
        .password-strength.weak { color: #b02a37; }
        .password-strength.medium { color: #997404; }
        .password-strength.strong { color: #146c43; }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            border: 0;
            white-space: nowrap;
        }

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
            <div class="register-card-wrap col-md-12 col-lg-12 col-xl-10 col-xxl-8">
                <div class="register-card">
            <div class="register-header">
                {{-- <img src="{{ asset('assets/images/nis.png') }}" alt="NIS Logo" class="register-logo"> --}}
                <h1 class="register-title">Create Account</h1>
                <p class="register-subtitle">Complete the form to request secure access to the NIS REDAS platform.</p>
            </div>

            <div class="register-body">
                @if ($errors->any())
                        <div class="alert alert-danger" role="alert" aria-live="polite">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                @if (session('status'))
                        <div class="alert alert-success" role="alert" aria-live="polite">
                        <i class="fas fa-check-circle me-2"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif
            
            <form id="registerForm" method="POST" action="{{ route('register.submit') }}" novalidate autocomplete="off">
                    @csrf
                    <!-- Full Name -->
                <div class="auth-form-group mt-0">
                    <label class="form-label-name" for="nameInput">
                        <i class="fas fa-id-card me-1 text-nis"></i> Full Name
                    </label>
                    <div class="auth-input-wrap">
                        <input type="text"
                            class="auth-input @error('name') is-invalid @enderror"
                            id="nameInput" name="name"
                            placeholder="John Doe"
                            value="{{ old('name') }}"
                            autocomplete="name"
                            required>
                        <span class="auth-input-icon"><i class="fas fa-id-badge"></i></span>
                    </div>
                    @error('register')
                    <div style="color:var(--color-danger);font-size:.76rem;margin-top:4px;"><i class="fas fa-circle-xmark me-1"></i>{{ $message }}</div>
                    @enderror
                </div>
                    <!-- Service Number -->
                    <div class="auth-form-group mt-3">
                    <label class="form-label-service" for="serviceNumber">
                        <i class="fas fa-shield-alt me-1 text-nis"></i> Service Number
                    </label>
                    <div class="auth-input-wrap">
                        <input type="text"
                            class="auth-input @error('service_number') is-invalid @enderror"
                            id="serviceNumber" name="service_number"
                            placeholder="NIS/AD/1234"
                            value="{{ old('service_number') }}"
                            autocomplete="off"
                            required>
                        <span class="auth-input-icon"><i class="fas fa-shield-alt"></i></span>
                    </div>
                    @error('service_number')
                    <div style="color:var(--color-danger);font-size:.76rem;margin-top:4px;"><i class="fas fa-circle-xmark me-1"></i>{{ $message }}</div>
                    @enderror
                </div>
                    
                    <!-- Role Selection dropdown btn -->
                    <div class="auth-form-group mt-3">
                    <label class="form-label-role" for="roleSelect">
                        <i class="fas fa-users me-1 text-nis"></i> Select Role  
                    </label>
                    <div class="auth-input-wrap">
                        <select id="roleSelect" name="role" class="auth-input @error('role') is-invalid @enderror" required>
                            <option value="" disabled selected>Select your role</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                            <option value="officer" {{ old('role') == 'officer' ? 'selected' : '' }}>Operational Records Officer</option>
                            <option value="archivist" {{ old('role') == 'archivist' ? 'selected' : '' }}>Archivist</option>
                        </select>
                        <span class="auth-input-icon"><i class="fas fa-users"></i></span>
                    </div>
                    @error('role')
                    <div style="color:var(--color-danger);font-size:.76rem;margin-top:4px;"><i class="fas fa-circle-xmark me-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                    <!-- Email -->
                    <div class="auth-form-group mt-3">
                    <label class="form-label-email" for="loginInput">
                        <i class="fas fa-id-card me-1 text-nis"></i> Email Address
                    </label>
                    <div class="auth-input-wrap">
                        <input type="text"
                            class="auth-input @error('email') is-invalid @enderror"
                            id="loginInput" name="login"
                            placeholder="officer@immigration.gov.ng"
                            value="{{ old('login') }}"
                            autocomplete="email"
                            required>
                        <span class="auth-input-icon"><i class="fas fa-id-badge"></i></span>
                    </div>
                    @error('email')
                    <div style="color:var(--color-danger);font-size:.76rem;margin-top:4px;"><i class="fas fa-circle-xmark me-1"></i>{{ $message }}</div>
                    @enderror
                </div>
                   
                    <!-- Password -->
                <div class="auth-form-group mt-3">
                    <label class="form-label-password" for="password">
                        <i class="fas fa-lock me-1 text-nis"></i> Password
                    </label>
                    <div class="auth-input-wrap pw-wrap">
                        <input type="password"
                            class="auth-input @error('password') is-invalid @enderror"
                            id="password" name="password"
                            placeholder="Create a strong password"
                            autocomplete="new-password"
                            required>
                        <button type="button" class="pw-toggle" data-target="password" aria-label="Show password">
                            <i class="fas fa-eye"></i>
                        </button>
                        <span class="auth-input-icon"><i class="fas fa-lock"></i></span>
                    </div>
                    <div id="passwordStrength" class="password-strength" aria-live="polite"></div>
                    @error('password')
                    <div style="color:var(--color-danger);font-size:.76rem;margin-top:4px;"><i class="fas fa-circle-xmark me-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                    <!-- Confirm Password -->
                <div class="auth-form-group mt-0">
                    <label class="form-label-confirm-password" for="passwordConfirmation">
                        <i class="fas fa-lock me-1 text-nis"></i> Confirm Password
                    </label>
                    <div class="auth-input-wrap pw-wrap">
                        <input type="password"
                            class="auth-input @error('password_confirmation') is-invalid @enderror"
                            id="passwordConfirmation" name="password_confirmation"
                            placeholder="Confirm your password"
                            autocomplete="new-password"
                            required>
                        <button type="button" class="pw-toggle" data-target="passwordConfirmation" aria-label="Show password">
                            <i class="fas fa-eye"></i>
                        </button>
                        <span class="auth-input-icon"><i class="fas fa-lock"></i></span>
                    </div>
                    @error('password_confirmation')
                    <div style="color:var(--color-danger);font-size:.76rem;margin-top:4px;"><i class="fas fa-circle-xmark me-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                    <!-- Terms & Conditions -->
                <div class="auth-form-group mt-3">
                    <div class="form-check">
                        <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" id="termsCheck" name="terms" required>
                        <label class="form-check-label" for="termsCheck">
                            I agree to the <a href="{{ route('terms') }}" target="_blank">Terms and Conditions</a> and <a href="{{ route('privacy') }}" target="_blank">Privacy Policy</a>. 
                        </label>
                        @error('terms')
                        <div style="color:var(--color-danger);font-size:.76rem;margin-top:4px;"><i class="fas fa-circle-xmark me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                    <!-- Register Button -->
                    <button type="submit" id="registerBtn" class="btn btn-primary w-100 mt-4">
                        <span class="btn-text">Create Account</span>
                    </button>

                </form>

                <p class="login-link mt-4">
                    Already have an account? <a href="{{ route('login') }}">Login here</a>
                </p>

                <div class="footer-links">
                    <a href="{{ url('/') }}"><i class="fas fa-home me-1"></i>Back to Home</a>
                </div>
            </div>
        </div>
        </div>
        </div>
        </div>
    </main>
</div>

<script>
            const registerForm = document.getElementById('registerForm');
            const registerButton = document.getElementById('registerBtn');
            const submitStateText = document.getElementById('submitStateText');
            const serviceNumberInput = document.getElementById('serviceNumber');
            const passwordInput = document.getElementById('password');
            const passwordConfirmationInput = document.getElementById('passwordConfirmation');
            const passwordStrength = document.getElementById('passwordStrength');

            function normalizeServiceNumber(value) {
                return value.toUpperCase().replace(/\s+/g, '');
            }

            function updatePasswordStrength() {
                if (!passwordInput || !passwordStrength) {
                    return;
                }

                const value = passwordInput.value;
                let score = 0;
                if (value.length >= 8) score++;
                if (/[A-Z]/.test(value) && /[a-z]/.test(value)) score++;
                if (/\d/.test(value)) score++;
                if (/[^A-Za-z0-9]/.test(value)) score++;

                passwordStrength.className = 'password-strength';

                if (!value.length) {
                    passwordStrength.textContent = '';
                    return;
                }

                if (score <= 2) {
                    passwordStrength.classList.add('weak');
                    passwordStrength.textContent = 'Password strength: Weak';
                    return;
                }

                if (score === 3) {
                    passwordStrength.classList.add('medium');
                    passwordStrength.textContent = 'Password strength: Medium';
                    return;
                }

                passwordStrength.classList.add('strong');
                passwordStrength.textContent = 'Password strength: Strong';
            }

            function validatePasswordMatch() {
                if (!passwordInput || !passwordConfirmationInput) {
                    return;
                }

                <script>
                // Registration form enhancements
                const registerForm = document.getElementById('registerForm');
                const registerButton = document.getElementById('registerBtn');
                const submitStateText = document.getElementById('submitStateText');
                const serviceNumberInput = document.getElementById('serviceNumber');
                const passwordInput = document.getElementById('password');
                const passwordConfirmationInput = document.getElementById('passwordConfirmation');
                const passwordStrength = document.getElementById('passwordStrength');
                }

                passwordConfirmationInput.setCustomValidity('');
            }

            function validateServiceNumber() {
                if (!serviceNumberInput) {
                    return;
                }

                serviceNumberInput.value = normalizeServiceNumber(serviceNumberInput.value);

                if (!serviceNumberInput.value) {
                    serviceNumberInput.setCustomValidity('');
                    return;
                }

                const servicePattern = /^NIS\/[A-Z]{2}\/[0-9]{4}$/;
                if (!servicePattern.test(serviceNumberInput.value)) {
                    serviceNumberInput.setCustomValidity('Service Number must be in the format NIS/XX/1234.');
                    return;
                }

                serviceNumberInput.setCustomValidity('');
            }

            document.querySelectorAll('.pw-toggle').forEach((button) => {
                button.addEventListener('click', () => {
                    const targetInput = document.getElementById(button.dataset.target);
                    if (!targetInput) {
                        return;
                    }

                    const isPassword = targetInput.type === 'password';
                    targetInput.type = isPassword ? 'text' : 'password';
                    button.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
                    const icon = button.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('fa-eye');
                        icon.classList.toggle('fa-eye-slash');
                    }
                });
            });

            serviceNumberInput?.addEventListener('input', validateServiceNumber);
            serviceNumberInput?.addEventListener('blur', validateServiceNumber);
            passwordInput?.addEventListener('input', () => {
                updatePasswordStrength();
                validatePasswordMatch();
            });
            passwordConfirmationInput?.addEventListener('input', validatePasswordMatch);

            registerForm?.addEventListener('submit', (event) => {
                validateServiceNumber();
                validatePasswordMatch();

                if (!registerForm.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    registerForm.classList.add('was-validated');
                    document.querySelector('.auth-right-inner')?.classList.add('shake');
                    return;
                }

                if (registerButton) {
                    registerButton.disabled = true;
                    const text = registerButton.querySelector('.btn-text');
                    if (text) {
                        text.innerHTML = '<i class="fas fa-circle-notch fa-spin me-2"></i>Creating Account...';
                    }
                    if (submitStateText) {
                        submitStateText.textContent = 'Submitting registration form';
                    }
                }
            });

            updatePasswordStrength();
            validateServiceNumber();

            /* Shake on server-side validation error */
            @if($errors->any())
            document.querySelector('.auth-right-inner')?.classList.add('shake');
            @endif
</script>
</body>
</html>
