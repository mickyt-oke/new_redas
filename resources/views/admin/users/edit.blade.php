@include('partials.header3')

<main class="redas-content" style="max-width:760px;margin:32px auto;padding:0 16px;">
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit User</h1>
            <p class="page-subtitle">Update account details, status, and geolocation override for this user.</p>
        </div>
        <a href="{{ route('admin.users') }}" class="btn-nis btn-ghost">
            <i class="fas fa-arrow-left"></i> Back to users
        </a>
    </div>

    @if(session('status'))
        <div class="alert alert-success" style="margin-bottom:16px;">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger" style="margin-bottom:16px;">
            <ul style="margin:0;padding-left:18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="redas-card">
        <div class="card-body" style="padding:24px;">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PATCH')

                <div class="auth-form-group" style="margin-bottom:18px;">
                    <label class="form-label-nis">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="auth-input" required>
                </div>

                <div class="auth-form-group" style="margin-bottom:18px;">
                    <label class="form-label-nis">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="auth-input" required>
                </div>

                <div class="auth-form-group" style="margin-bottom:18px;">
                    <label class="form-label-nis">Service Number</label>
                    <input type="text" name="service_number" value="{{ old('service_number', $user->service_number) }}" class="auth-input" required>
                </div>

                <div class="auth-form-group" style="margin-bottom:18px;">
                    <label class="form-label-nis">Role</label>
                    <select name="role" class="auth-input" required>
                        @foreach($roles as $value => $label)
                            <option value="{{ $value }}" {{ old('role', $user->role) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="auth-form-group" style="margin-bottom:18px;">
                    <label class="form-label-nis">Access Category</label>
                    <select name="user_category" class="auth-input" required>
                        @foreach($categories as $value => $label)
                            <option value="{{ $value }}" {{ old('user_category', $user->user_category) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="auth-form-group" style="margin-bottom:18px;">
                    <label class="form-label-nis">Primary Location Type</label>
                    <select name="primary_location_type" class="auth-input" required>
                        @foreach($locationTypes as $value => $label)
                            <option value="{{ $value }}" {{ old('primary_location_type', $user->primary_location_type) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="auth-form-group" style="margin-bottom:18px;">
                    <label class="form-label-nis">Primary Location Code</label>
                    <input type="text" name="primary_location_code" value="{{ old('primary_location_code', $user->primary_location_code) }}" class="auth-input" placeholder="e.g. LA, AB, HRM">
                </div>

                <div class="auth-form-group" style="margin-bottom:18px;">
                    <label class="form-label-nis">State Override</label>
                    <input type="text" name="geo_state" value="{{ old('geo_state', $user->geo_state) }}" class="auth-input" placeholder="Optional state code">
                    <small class="text-muted">Set a required login state override for this user.</small>
                </div>

                <div class="auth-form-group" style="margin-bottom:18px;display:flex;align-items:center;gap:12px;">
                    <label class="switch">
                        <input type="checkbox" name="is_enabled" value="1" {{ old('is_enabled', $user->is_enabled) ? 'checked' : '' }}>
                        <span class="slider round"></span>
                    </label>
                    <span>Account enabled</span>
                </div>

                <div class="auth-form-group" style="margin-bottom:18px;">
                    <label class="form-label-nis">New Password</label>
                    <input type="password" name="password" class="auth-input" placeholder="Leave blank to keep current password">
                </div>

                <div class="auth-form-group" style="margin-bottom:24px;">
                    <label class="form-label-nis">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="auth-input">
                </div>

                <button type="submit" class="btn-nis btn-primary-nis full-width">
                    Save changes
                </button>
            </form>
        </div>
    </div>
</main>

@include('partials.footer')
