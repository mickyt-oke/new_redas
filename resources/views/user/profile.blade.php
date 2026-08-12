@include('partials.header')

<main class="redas-content" style="max-width:720px;margin:32px auto;padding:0 16px;">
    <div class="page-header">
        <div>
            <h1 class="page-title">Profile</h1>
            <p class="page-subtitle">Update your password. Your user details cannot be changed here.</p>
        </div>
        <a href="{{ url()->previous() ?? route('user.dashboard') }}" class="btn-nis btn-ghost">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    @if(session('status'))
        <div class="alert alert-success" style="margin-bottom:16px;">{{ session('status') }}</div>
    @endif

    <div class="redas-card">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:#e0f2fe;color:#0369a1;"><i class="fas fa-user-lock"></i></div>
                Update Password
            </div>
        </div>
        <div class="card-body" style="padding:24px;">
            <form method="POST" action="{{ route('user.profile.update') }}">
                @csrf
                @method('PATCH')

                <div class="auth-form-group" style="margin-bottom:18px;">
                    <label class="form-label-nis">Your Name</label>
                    <input type="text" class="auth-input" value="{{ $user->name }}" disabled>
                </div>

                <div class="auth-form-group" style="margin-bottom:18px;">
                    <label class="form-label-nis">Email Address</label>
                    <input type="email" class="auth-input" value="{{ $user->email }}" disabled>
                </div>

                <div class="auth-form-group" style="margin-bottom:18px;">
                    <label class="form-label-nis">Current Password</label>
                    <div class="auth-input-wrap">
                        <input type="password" name="current_password" class="auth-input" placeholder="Enter your current password" required>
                        <span class="auth-input-icon"><i class="fas fa-key"></i></span>
                    </div>
                    @error('current_password')
                        <div class="inline-error"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>
                    @enderror
                </div>

                <div class="auth-form-group" style="margin-bottom:18px;">
                    <label class="form-label-nis">New Password</label>
                    <div class="auth-input-wrap">
                        <input type="password" name="password" class="auth-input" placeholder="Enter new password" required>
                        <span class="auth-input-icon"><i class="fas fa-lock"></i></span>
                    </div>
                    @error('password')
                        <div class="inline-error"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>
                    @enderror
                </div>

                <div class="auth-form-group" style="margin-bottom:24px;">
                    <label class="form-label-nis">Confirm New Password</label>
                    <div class="auth-input-wrap">
                        <input type="password" name="password_confirmation" class="auth-input" placeholder="Confirm new password" required>
                        <span class="auth-input-icon"><i class="fas fa-lock"></i></span>
                    </div>
                </div>

                <button type="submit" class="btn-nis btn-primary-nis full-width">
                    <i class="fas fa-sync-alt"></i> Update password
                </button>
            </form>
        </div>
    </div>
</main>

@include('partials.footer')
