<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - REDAS Secure Access Gateway</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    {{-- Bootstrap & Font Awesome are bundled via Vite --}}
    <link rel="icon" type="image/png" href="{{ asset('assets/images/nis.png') }}">
    @vite(['resources/js/app.js', 'resources/css/app.css', 'resources/css/auth-pages.css', 'resources/css/styles.css'])
</head>
<body class="register-page" data-page="register">

    <!-- Header -->
    <header class="bg-white shadow-sm border-bottom">
        <nav class="navbar navbar-expand-lg navbar-light container">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <img src="{{ asset('assets/images/nis.png') }}" alt="NIS Logo" height="40" class="me-2">
                <span class="fw-bold text-success">NIS REDAS</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('register') }}">Register</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <img src="{{ asset('assets/images/nis.png') }}" alt="NIS Logo" class="register-logo">
                <h1 class="register-title">Create Account</h1>
                <p class="register-subtitle">Join REDAS Secure Gateway</p>
            </div>

            <div class="register-body">
                <!-- Info Alert -->
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    Register with your official NIS service number.
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <form id="registerForm" method="POST" action="{{ route('register.submit') }}" novalidate>
                    @csrf
                    <!-- Full Name -->
                    <div class="form-floating">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Full Name" value="{{ old('name') }}" required>
                        <label for="name"><i class="fas fa-user me-2"></i>Full Name</label>
                    </div>

                    <!-- Service Number -->
                    <div class="form-floating">
                        <input type="text" class="form-control @error('service_number') is-invalid @enderror" id="serviceNumber" name="service_number" placeholder="Service Number" value="{{ old('service_number') }}" required>
                        <label for="serviceNumber"><i class="fas fa-id-card me-2"></i>Service Number (NIS/XX/XXXX)</label>
                    </div>

                    <!-- Role Selection -->
                    <div class="form-floating">
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                            <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select your role</option>
                            <option value="officer" {{ old('role') === 'officer' ? 'selected' : '' }}>State User</option>
                            <option value="directorate" {{ old('role') === 'directorate' ? 'selected' : '' }}>Directorate User</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                            <option value="zonal" {{ old('role') === 'zonal' ? 'selected' : '' }}>Zonal Commander</option>
                            <option value="state" {{ old('role') === 'state' ? 'selected' : '' }}>State Supervisor</option>
                        </select>
                        <label for="role"><i class="fas fa-user-shield me-2"></i>Select Role</label>
                    </div>

                    <!-- Email -->
                    <div class="form-floating">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Email Address" value="{{ old('email') }}" required>
                        <label for="email"><i class="fas fa-envelope me-2"></i>Official Email Address</label>
                    </div>

                    <!-- Password -->
                    <div class="form-floating">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" required minlength="8">
                        <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                    </div>
                    <div class="password-strength" id="passwordStrength"></div>
                    <small class="text-muted d-block mb-3">Password must be at least 8 characters</small>

                    <!-- Confirm Password -->
                    <div class="form-floating">
                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="passwordConfirmation" name="password_confirmation" placeholder="Confirm Password" required>
                        <label for="passwordConfirmation"><i class="fas fa-lock me-2"></i>Confirm Password</label>
                    </div>

                    <!-- Terms & Conditions -->
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" id="terms" name="terms" value="1" {{ old('terms') ? 'checked' : '' }} required>
                            <label class="form-check-label text-muted" for="terms">
                                I agree to the <a href="#" class="terms-link">Terms &amp; Conditions</a> and <a href="#" class="terms-link">Privacy Policy</a>
                            </label>
                        </div>
                    </div>

                    <!-- Register Button -->
                    <button type="submit" class="btn btn-register w-100" id="registerBtn">
                        <span class="btn-text"><i class="fas fa-user-plus me-2"></i>Create Account</span>
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

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="fw-bold">Nigeria Immigration Service</h5>
                    <p class="mb-0">Service Headquarters, Abuja, FCT</p>
                    <p class="mb-0">Email: info@immigration.gov.ng</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">&copy; 2026 Nigeria Immigration Service. All Rights Reserved.</p>
                    <a href="https://immigration.gov.ng" class="text-white-50" target="_blank">Visit Official Website</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
