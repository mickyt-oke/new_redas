@include('partials.header')

<main class="redas-content" style="max-width:720px;margin:32px auto;padding:0 16px;">
    <div class="page-header">
        <div>
            <h1 class="page-title">Profile</h1>
            <p class="page-subtitle">Update your password and lockscreen passcode. Your user details cannot be changed here.</p>
        </div>
        <a href="{{ url()->previous() ?? route('user.dashboard') }}" class="btn-nis btn-ghost">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    @if(session('status'))
        <div class="alert alert-success" style="margin-bottom:16px;">{{ session('status') }}</div>
    @endif

    @if($user->must_change_password)
        <div class="alert" style="margin-bottom:16px;background:#fffbeb;border:1px solid #fcd34d;color:#92400e;padding:12px 16px;border-radius:8px;">
            <i class="fas fa-triangle-exclamation"></i>
            For your security, you must change your password before you can continue using the system.
        </div>
    @endif

    {{-- Password change card --}}
    <div class="redas-card" style="margin-bottom:20px;">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:#e0f2fe;color:#0369a1;"><i class="fas fa-user-lock"></i></div>
                Change Password
            </div>
        </div>
        <div class="card-body" style="padding:24px;">
            <form method="POST" action="{{ route('user.profile.update') }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="section" value="password">

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
                    <div class="auth-input-wrap pw-wrap">
                        <input type="password" name="current_password" class="auth-input" placeholder="Enter your current password" required>
                        <span class="auth-input-icon"><i class="fas fa-lock"></i></span>
                        <button type="button" class="pw-toggle" aria-label="Toggle password visibility">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <div class="inline-error"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>
                    @enderror
                </div>

                <div class="auth-form-group" style="margin-bottom:18px;">
                    <label class="form-label-nis">New Password</label>
                    <div class="auth-input-wrap pw-wrap">
                        <input type="password" name="password" class="auth-input" placeholder="Enter new password" {{ $user->must_change_password ? 'required' : '' }}>
                        <span class="auth-input-icon"><i class="fas fa-lock"></i></span>
                        <button type="button" class="pw-toggle" aria-label="Toggle password visibility">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="inline-error"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>
                    @enderror
                </div>

                <div class="auth-form-group" style="margin-bottom:24px;">
                    <label class="form-label-nis">Confirm New Password</label>
                    <div class="auth-input-wrap pw-wrap">
                        <input type="password" name="password_confirmation" class="auth-input" placeholder="Confirm new password" {{ $user->must_change_password ? 'required' : '' }}>
                        <span class="auth-input-icon"><i class="fas fa-lock"></i></span>
                        <button type="button" class="pw-toggle" aria-label="Toggle password visibility">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-nis btn-primary-nis full-width">
                    <i class="fas fa-sync-alt"></i> Update password
                </button>
            </form>
        </div>
    </div>

    {{-- Lockscreen passcode card --}}
    <div class="redas-card" style="margin-bottom:20px;">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:#f0fdf4;color:#15803d;"><i class="fas fa-key"></i></div>
                Lockscreen Passcode
            </div>
            @if($user->lockscreen_passcode)
                <span class="status-badge badge-approved" style="font-size:.7rem;">Passcode set</span>
            @else
                <span class="status-badge badge-draft" style="font-size:.7rem;">Not set</span>
            @endif
        </div>
        <div class="card-body" style="padding:24px;">
            <form method="POST" action="{{ route('user.profile.update') }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="section" value="passcode">

                <p style="font-size:.82rem;color:var(--gray-600);margin:0 0 16px;">
                    <i class="fas fa-info-circle" style="color:var(--nis-600);"></i>
                    This passcode is used to unlock your session from the lock screen. If you do not set one, your main password will be required.
                </p>

                <div class="auth-form-group" style="margin-bottom:18px;">
                    <label class="form-label-nis">Current Password</label>
                    <div class="auth-input-wrap pw-wrap">
                        <input type="password" name="current_password" class="auth-input" placeholder="Enter your current password" required>
                        <span class="auth-input-icon"><i class="fas fa-lock"></i></span>
                        <button type="button" class="pw-toggle" aria-label="Toggle password visibility">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <div class="inline-error"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>
                    @enderror
                </div>

                <div class="auth-form-group" style="margin-bottom:18px;">
                    <label class="form-label-nis">New Lockscreen Passcode</label>
                    <div class="auth-input-wrap pw-wrap">
                        <input type="password" name="lockscreen_passcode" class="auth-input" placeholder="4–20 characters" autocomplete="off">
                        <span class="auth-input-icon"><i class="fas fa-key"></i></span>
                        <button type="button" class="pw-toggle" aria-label="Toggle passcode visibility">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('lockscreen_passcode')
                        <div class="inline-error"><i class="fas fa-circle-xmark"></i>{{ $message }}</div>
                    @enderror
                </div>

                <div class="auth-form-group" style="margin-bottom:18px;">
                    <label class="form-label-nis">Confirm Lockscreen Passcode</label>
                    <div class="auth-input-wrap pw-wrap">
                        <input type="password" name="lockscreen_passcode_confirmation" class="auth-input" placeholder="Confirm passcode" autocomplete="off">
                        <span class="auth-input-icon"><i class="fas fa-key"></i></span>
                        <button type="button" class="pw-toggle" aria-label="Toggle passcode visibility">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                @if($user->lockscreen_passcode)
                    <div class="auth-form-group" style="margin-bottom:24px;">
                        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:.84rem;color:var(--gray-700);">
                            <input type="checkbox" name="remove_lockscreen_passcode" value="1" style="accent-color:var(--nis-600);">
                            <span>Remove my lockscreen passcode (I want to use my main password instead)</span>
                        </label>
                    </div>
                @endif

                <button type="submit" class="btn-nis btn-primary-nis full-width">
                    <i class="fas fa-save"></i> Save passcode
                </button>
            </form>
        </div>
    </div>
</main>

@include('partials.footer')
