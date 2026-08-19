@php
    $user = auth()->user();
    $loc = strtoupper($user->primary_location_code ?? '');
    $category = $user->user_category;

    if ($category === 'admin' || $category === 'super_admin') {
        $header = 'partials.header3';
    } elseif ($loc === 'VISA') {
        $header = 'partials.visa-header';
    } elseif ($loc === 'ICT' || $loc === 'ICT_CYBERSECURITY') {
        $header = 'partials.ict-header';
    } else {
        $header = 'partials.header';
    }
@endphp
@include($header)

<main class="redas-content animate-fade-up">

    <!-- Page Header -->
    <div class="page-header" style="margin-bottom:24px;">
        <div>
            <h1 class="page-title">
                <i class="fas fa-user-gear" style="color:var(--nis-600);"></i>
                Account Settings
            </h1>
            <p class="page-subtitle">Manage your personal profile information, system location parameters, and security credentials.</p>
        </div>
    </div>

    <!-- Alert messages -->
    @if(session('status'))
        <div class="profile-alert success">
            <i class="fas fa-check-circle"></i>
            <div>{{ session('status') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="profile-alert error">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <strong style="font-size:0.85rem; display:block; margin-bottom:4px;">Please correct the errors:</strong>
                <ul style="margin:0 0 0 16px; padding:0; font-size:0.8rem; line-height:1.4;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Profile Grid Layout -->
    <div class="profile-layout-grid">
        
        <!-- Column 1: Vertical Sidebar Navigation -->
        <div class="profile-nav-card">
            <!-- User Summary Header -->
            <div class="user-profile-summary">
                <div class="user-avatar-wrapper">
                    @if($user->profile_picture)
                        <img src="/storage/{{ $user->profile_picture }}" class="user-avatar-img" alt="Avatar">
                    @else
                        <div class="user-avatar-placeholder">
                            {{ strtoupper(substr($user->name ?? 'U', 0, 2)) }}
                        </div>
                    @endif
                    <div class="avatar-ring"></div>
                </div>
                <h3 class="user-summary-name">{{ $user->name }}</h3>
                <p class="user-summary-meta">Service No: <span>{{ $user->service_number }}</span></p>
                @php
                    $dirCode = strtoupper($user->primary_location_code ?? '');
                    $dirName = $dirCode === 'ICT' ? 'ICT & Cybersecurity' : ($dirCode === 'VISA' ? 'Visa Directorate' : '');
                    if ($user->user_category === 'directorate_user') {
                        $roleBadge = $dirName ? "$dirName Desk Officer" : "Desk Officer";
                    } elseif ($user->user_category === 'directorate_admin') {
                        $roleBadge = $dirName ? "$dirName Admin" : "Admin";
                    } else {
                        $roleBadge = str_replace('_', ' ', $user->user_category);
                    }
                @endphp
                <div class="user-badge">{{ $roleBadge }}</div>
            </div>

            <!-- Vertical Navigation Tabs -->
            <div class="profile-tabs-vertical">
                <button onclick="switchTab('profile')" id="tab-profile" class="tab-btn active">
                    <i class="fas fa-id-card"></i>
                    <span>Profile Details</span>
                    <i class="fas fa-chevron-right tab-arrow"></i>
                </button>
                <button onclick="switchTab('settings')" id="tab-settings" class="tab-btn">
                    <i class="fas fa-key"></i>
                    <span>Security &amp; Settings</span>
                    <i class="fas fa-chevron-right tab-arrow"></i>
                </button>
            </div>
        </div>

        <!-- Column 2: Tab Content Area -->
        <div class="profile-content-panel">
            
            <!-- Pane 1: Profile Details -->
            <div id="content-profile" class="tab-content active">
                <div class="redas-card profile-card-modern">
                    <div class="card-head">
                        <div class="card-head-title">
                            <div class="card-head-icon" style="background:var(--nis-50); color:var(--nis-600);">
                                <i class="fas fa-user-pen"></i>
                            </div>
                            Personal Information
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="profile-form-group">
                                <label class="profile-form-label">Profile Avatar / Photograph</label>
                                <div class="avatar-upload-control">
                                    @if($user->profile_picture)
                                        <img src="/storage/{{ $user->profile_picture }}" class="upload-avatar-preview">
                                    @else
                                        <div class="upload-avatar-fallback">
                                            {{ strtoupper(substr($user->name ?? 'U', 0, 2)) }}
                                        </div>
                                    @endif
                                    <div class="upload-btn-wrapper">
                                        <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
                                        <label for="profile_picture" class="file-upload-trigger">
                                            <i class="fas fa-cloud-upload-alt"></i> Choose File
                                        </label>
                                        <span class="upload-tip">Formats: JPEG, PNG, JPG (Max: 2MB).</span>
                                    </div>
                                </div>
                            </div>

                            <div class="profile-form-group">
                                <label class="profile-form-label">Full Name</label>
                                <div class="input-icon-wrapper">
                                    <i class="fas fa-user input-icon"></i>
                                    <input type="text" name="name" class="profile-input" value="{{ old('name', $user->name) }}" required>
                                </div>
                            </div>

                            <div class="profile-form-group">
                                <label class="profile-form-label">Email Address</label>
                                <div class="input-icon-wrapper">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <input type="email" name="email" class="profile-input" value="{{ old('email', $user->email) }}" required>
                                </div>
                            </div>

                            <div class="profile-form-group">
                                <label class="profile-form-label" style="color:var(--gray-400);">Primary Formation / Location</label>
                                <div class="input-icon-wrapper">
                                    <i class="fas fa-building input-icon" style="color:var(--gray-400);"></i>
                                    <input type="text" class="profile-input disabled" value="{{ $user->primary_location_type }} ({{ $user->primary_location_code }})" disabled>
                                </div>
                                <span class="profile-input-help"><i class="fas fa-lock"></i> Account location parameters are system locked. Contact HQ to update.</span>
                            </div>

                            <div style="margin-top:24px; padding-top:16px; border-top:1px solid var(--gray-100);">
                                <button type="submit" class="profile-submit-btn">
                                    <i class="fas fa-save"></i> Save Profile Details
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Pane 2: Security Settings -->
            <div id="content-settings" class="tab-content">
                <div class="redas-card profile-card-modern">
                    <div class="card-head">
                        <div class="card-head-title">
                            <div class="card-head-icon" style="background:var(--nis-50); color:var(--nis-600);">
                                <i class="fas fa-shield-halved"></i>
                            </div>
                            Security &amp; Password
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="section-intro-desc">
                            Update your system credentials regularly to maintain security access clearance.
                        </p>

                        <form action="{{ route('user.profile.update') }}" method="POST">
                            @csrf
                            
                            <div class="profile-form-group">
                                <label class="profile-form-label">Current Password</label>
                                <div class="input-icon-wrapper">
                                    <i class="fas fa-lock-open input-icon"></i>
                                    <input type="password" name="current_password" class="profile-input" placeholder="••••••••" required>
                                </div>
                            </div>

                            <div class="profile-form-group">
                                <label class="profile-form-label">New Password</label>
                                <div class="input-icon-wrapper">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password" name="new_password" class="profile-input" placeholder="•••••••• (Min. 8 characters)" required>
                                </div>
                            </div>

                            <div class="profile-form-group">
                                <label class="profile-form-label">Confirm New Password</label>
                                <div class="input-icon-wrapper">
                                    <i class="fas fa-circle-check input-icon"></i>
                                    <input type="password" name="new_password_confirmation" class="profile-input" placeholder="••••••••" required>
                                </div>
                            </div>

                            <div style="margin-top:24px; padding-top:16px; border-top:1px solid var(--gray-100);">
                                <button type="submit" class="profile-submit-btn">
                                    <i class="fas fa-key"></i> Update Credentials
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>

    </div>

</main>

<style>
/* CSS Variables & Theme Alignment */
:root {
    --avatar-ring-color: rgba(3, 84, 52, 0.15);
}

/* Page Alert Styles */
.profile-alert {
    display: flex;
    gap: 12px;
    align-items: flex-start;
    padding: 14px 16px;
    border-radius: 12px;
    margin-bottom: 20px;
    font-size: 0.85rem;
}
.profile-alert.success {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #15803d;
}
.profile-alert.error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #b91c1c;
}

/* Grid Layout */
.profile-layout-grid {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 24px;
    align-items: start;
    margin-bottom: 40px;
}

/* Sidebar Navigation Card */
.profile-nav-card {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 16px;
    padding: 24px;
    box-shadow: var(--shadow-sm);
}

/* User Summary Header */
.user-profile-summary {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--gray-100);
    margin-bottom: 20px;
}
.user-avatar-wrapper {
    position: relative;
    width: 84px;
    height: 84px;
    margin-bottom: 14px;
}
.user-avatar-img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid white;
    box-shadow: var(--shadow-md);
    z-index: 2;
    position: relative;
}
.user-avatar-placeholder {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--nis-600), var(--nis-800));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: 700;
    border: 3px solid white;
    box-shadow: var(--shadow-md);
    z-index: 2;
    position: relative;
}
.avatar-ring {
    position: absolute;
    top: -5px;
    left: -5px;
    right: -5px;
    bottom: -5px;
    border-radius: 50%;
    border: 2px dashed var(--nis-600);
    opacity: 0.3;
    animation: rotate 20s linear infinite;
}
@keyframes rotate {
    100% { transform: rotate(360deg); }
}

.user-summary-name {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--gray-800);
    margin: 0;
    line-height: 1.3;
}
.user-summary-meta {
    font-size: 0.78rem;
    color: var(--gray-500);
    margin: 4px 0 0;
}
.user-summary-meta span {
    font-family: monospace;
    font-weight: 700;
    color: var(--gray-700);
}
.user-badge {
    display: inline-block;
    margin-top: 10px;
    background: var(--nis-50);
    color: var(--nis-700);
    border: 1px solid var(--nis-200);
    font-size: 0.68rem;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Vertical Sidebar Tab List */
.profile-tabs-vertical {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.profile-tabs-vertical .tab-btn {
    background: transparent;
    border: none;
    padding: 12px 14px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--gray-500);
    cursor: pointer;
    text-align: left;
    transition: all 0.2s ease-in-out;
}
.profile-tabs-vertical .tab-btn:hover {
    background: var(--gray-50);
    color: var(--gray-800);
}
.profile-tabs-vertical .tab-btn.active {
    background: var(--nis-50);
    color: var(--nis-700);
}
.tab-arrow {
    margin-left: auto;
    font-size: 0.72rem;
    opacity: 0;
    transform: translateX(-4px);
    transition: all 0.2s ease;
}
.profile-tabs-vertical .tab-btn.active .tab-arrow {
    opacity: 1;
    transform: translateX(0);
}

/* Card Improvements */
.profile-card-modern {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 16px;
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}

/* Forms & Fields Styling */
.profile-form-group {
    margin-bottom: 20px;
}
.profile-form-label {
    display: block;
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 8px;
}
.section-intro-desc {
    font-size: 0.82rem;
    color: var(--gray-500);
    margin-bottom: 20px;
    line-height: 1.5;
}

/* Input Icon Wrappers */
.input-icon-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}
.input-icon {
    position: absolute;
    left: 14px;
    font-size: 0.9rem;
    color: var(--gray-400);
    pointer-events: none;
}
.profile-input {
    width: 100%;
    height: 42px;
    padding: 0 16px 0 40px;
    background: white;
    border: 1px solid var(--gray-300);
    border-radius: 10px;
    font-size: 0.88rem;
    color: var(--gray-800);
    outline: none;
    transition: all 0.15s ease-in-out;
}
.profile-input::placeholder {
    color: var(--gray-400);
}
.profile-input:focus {
    border-color: var(--nis-500);
    box-shadow: 0 0 0 4px rgba(3, 84, 52, 0.08);
}
.profile-input.disabled {
    background: var(--gray-50);
    border-color: var(--gray-200);
    color: var(--gray-500);
    cursor: not-allowed;
}

/* Dynamic Upload Control */
.avatar-upload-control {
    display: flex;
    align-items: center;
    gap: 16px;
    background: var(--gray-50);
    border: 1px dashed var(--gray-300);
    border-radius: 12px;
    padding: 14px;
}
.upload-avatar-preview {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid white;
    box-shadow: var(--shadow-sm);
}
.upload-avatar-fallback {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--nis-600), var(--nis-800));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.4rem;
}
.upload-btn-wrapper {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.upload-btn-wrapper input[type="file"] {
    display: none;
}
.file-upload-trigger {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: white;
    border: 1px solid var(--gray-300);
    color: var(--gray-700);
    font-size: 0.78rem;
    font-weight: 700;
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.15s ease;
    width: fit-content;
}
.file-upload-trigger:hover {
    background: var(--gray-50);
    border-color: var(--gray-400);
}
.upload-tip {
    font-size: 0.7rem;
    color: var(--gray-400);
}
.profile-input-help {
    display: block;
    font-size: 0.72rem;
    color: var(--gray-400);
    margin-top: 6px;
}

/* Submit Action Button styling */
.profile-submit-btn {
    width: 100%;
    height: 38px;
    background: var(--nis-600);
    border: 1px solid var(--nis-700);
    color: white;
    font-size: 0.82rem;
    font-weight: 700;
    border-radius: 10px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: var(--shadow-sm);
    transition: all 0.15s ease-in-out;
}
.profile-submit-btn:hover {
    background: var(--nis-700);
    border-color: var(--nis-800);
    box-shadow: var(--shadow-md);
}

/* Tab Switching visibility classes */
.tab-content {
    display: none;
}
.tab-content.active {
    display: block;
}

/* Premium Animations */
.animate-fade-in {
    animation: fadeIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(6px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive grid collapsing */
@media (max-width: 768px) {
    .profile-layout-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
}
</style>

<script>
function switchTab(tabName) {
    // Deactivate all contents
    document.querySelectorAll('.tab-content').forEach(el => {
        el.classList.remove('active', 'animate-fade-in');
    });
    // Deactivate all sidebar tab buttons
    document.querySelectorAll('.profile-tabs-vertical .tab-btn').forEach(el => {
        el.classList.remove('active');
    });
    
    // Activate clicked targets
    const content = document.getElementById('content-' + tabName);
    const button = document.getElementById('tab-' + tabName);
    if (content && button) {
        content.classList.add('active', 'animate-fade-in');
        button.classList.add('active');
    }
    
    // Smooth URL Hash update without page jump
    if (history.pushState) {
        history.pushState(null, null, '#' + tabName);
    } else {
        window.location.hash = tabName;
    }
}

function loadTabFromHash() {
    const hash = window.location.hash.replace('#', '');
    if (hash === 'settings') {
        switchTab('settings');
    } else {
        switchTab('profile');
    }
}

// Add event listeners for dynamic switching
window.addEventListener('DOMContentLoaded', loadTabFromHash);
window.addEventListener('hashchange', loadTabFromHash);
</script>

@include('partials.footer')
