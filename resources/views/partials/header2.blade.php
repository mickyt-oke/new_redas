<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Supervisor Dashboard | NIS-REDAS</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/nis.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="redas-dashboard">

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ════════════════════ SIDEBAR ════════════════════ -->
<aside class="redas-sidebar" id="redasSidebar">
    <a href="{{ route('supervisor.dashboard') }}" class="sidebar-brand">
        <img src="{{ asset('assets/images/nis.png') }}" alt="NIS" class="sidebar-brand-logo">
        <div class="sidebar-brand-text">
            <span class="sidebar-brand-title">NIS&nbsp;REDAS</span>
            <span class="sidebar-brand-sub">Supervisor Portal</span>
        </div>
    </a>

    <nav class="sidebar-nav">
        <div class="sidebar-section-label">Main Menu</div>

        <a href="{{ route('supervisor.dashboard') }}" class="sidebar-link active">
            <span class="link-icon"><i class="fas fa-tachometer-alt"></i></span>
            <span class="link-text">Dashboard</span>
        </a>

        <a href="{{ url('/supervisor/approvals') }}" class="sidebar-link">
            <span class="link-icon"><i class="fas fa-tasks"></i></span>
            <span class="link-text">Pending Approvals</span>
            <span class="link-badge danger">5</span>
        </a>

        <a href="{{ url('/supervisor/formations') }}" class="sidebar-link">
            <span class="link-icon"><i class="fas fa-map-marker-alt"></i></span>
            <span class="link-text">Formation Status</span>
        </a>

        <a href="{{ url('/supervisor/reports') }}" class="sidebar-link">
            <span class="link-icon"><i class="fas fa-chart-bar"></i></span>
            <span class="link-text">Zone Analytics</span>
        </a>

        <a href="{{ url('/supervisor/notifications') }}" class="sidebar-link">
            <span class="link-icon"><i class="fas fa-bell"></i></span>
            <span class="link-text">Notifications</span>
            <span class="link-badge">3</span>
        </a>

        <hr class="sidebar-divider">
        <div class="sidebar-section-label">Management</div>

        <a href="{{ url('/supervisor/archive') }}" class="sidebar-link">
            <span class="link-icon"><i class="fas fa-archive"></i></span>
            <span class="link-text">Document Archive</span>
        </a>

        <a href="{{ url('/supervisor/officers') }}" class="sidebar-link">
            <span class="link-icon"><i class="fas fa-users"></i></span>
            <span class="link-text">Formation Officers</span>
        </a>

        <hr class="sidebar-divider">
        <div class="sidebar-section-label">Account</div>

        <a href="{{ url('/supervisor/profile') }}" class="sidebar-link">
            <span class="link-icon"><i class="fas fa-user-cog"></i></span>
            <span class="link-text">Profile Settings</span>
        </a>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="sidebar-link" style="width:100%;background:none;border:none;cursor:pointer;color:rgba(255,255,255,0.72);text-align:left;">
                <span class="link-icon"><i class="fas fa-sign-out-alt"></i></span>
                <span class="link-text" style="color:rgba(255,100,100,0.85);">Logout</span>
            </button>
        </form>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user-card">
            <div class="sidebar-user-avatar" style="background:var(--gold-500);">
                {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 2)) }}
            </div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ auth()->user()->name ?? 'Supervisor' }}</div>
                <div class="sidebar-user-role">
                    @if(auth()->user()->role === 'zonal') Zonal Commander @else State Supervisor @endif
                </div>
            </div>
        </div>
    </div>
</aside>

<!-- ════════════════════ MAIN ════════════════════ -->
<div class="redas-main" id="redasMain">

    <!-- Topbar -->
    <header class="redas-topbar">
        <button class="topbar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
            <i class="fas fa-bars"></i>
        </button>

        <div class="topbar-breadcrumb">
            <span>NIS-REDAS</span>
            <span class="separator"><i class="fas fa-chevron-right" style="font-size:.6rem;"></i></span>
            <span class="current">Supervisor Dashboard</span>
        </div>

        <div class="topbar-right">
            <!-- Alert: pending approvals -->
            <span style="background:#fee2e2;color:#dc2626;border:1px solid #fca5a5;padding:5px 12px;border-radius:var(--radius-full);font-size:.72rem;font-weight:700;display:flex;align-items:center;gap:6px;animation:blink 2s infinite;">
                <i class="fas fa-exclamation-circle"></i> 5 Pending Approvals
            </span>

            <!-- Notifications -->
            <div style="position:relative;">
                <button class="topbar-icon-btn" id="notifBtn" aria-label="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="notif-dot"></span>
                </button>
                <div id="notifPanel" style="display:none;position:absolute;right:0;top:calc(100% + 8px);width:300px;background:white;border-radius:var(--radius-lg);box-shadow:var(--shadow-xl);border:1px solid var(--gray-200);z-index:200;overflow:hidden;">
                    <div style="padding:12px 16px;font-size:.82rem;font-weight:700;color:var(--gray-800);border-bottom:1px solid var(--gray-100);">Notifications</div>
                    @foreach([
                        ['danger', 'fas fa-file-alt', '5 returns awaiting your approval', 'Abuja State, Kano State, Lagos State, Rivers State, Ogun State', '1h ago'],
                        ['warning','fas fa-clock',    'Kaduna State submission overdue',   'Kaduna State monthly return is 3 days overdue.', '6h ago'],
                        ['success','fas fa-check',    'HQ approved your forwarded data',   'Q1 2025 zonal aggregate approved by PRS Directorate.', '2d ago'],
                    ] as [$type, $icon, $title, $desc, $time])
                    <div class="notif-item unread">
                        <div class="notif-icon" style="background:{{ $type === 'danger' ? '#fee2e2' : ($type === 'warning' ? '#fef9c3' : '#dcfce7') }};color:{{ $type === 'danger' ? '#dc2626' : ($type === 'warning' ? '#a16207' : '#15803d') }};">
                            <i class="{{ $icon }}"></i>
                        </div>
                        <div class="notif-content">
                            <div class="notif-title">{{ $title }}</div>
                            <div class="notif-desc">{{ $desc }}</div>
                        </div>
                        <div class="notif-time">{{ $time }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- User menu -->
            <div style="position:relative;">
                <button class="topbar-user" id="userMenuBtn" style="border:none;background:transparent;cursor:pointer;">
                    <div class="topbar-avatar" style="background:var(--gold-500);">
                        {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 2)) }}
                    </div>
                    <div class="topbar-user-info" style="text-align:left;">
                        <div class="topbar-user-name">{{ auth()->user()->name ?? 'Supervisor' }}</div>
                        <div class="topbar-user-role">{{ auth()->user()->role === 'zonal' ? 'Zonal Commander' : 'State Supervisor' }}</div>
                    </div>
                    <i class="fas fa-chevron-down" style="font-size:.7rem;color:var(--gray-400);margin-left:4px;"></i>
                </button>
                <div id="userMenuDrop" style="display:none;position:absolute;right:0;top:calc(100% + 8px);width:200px;background:white;border-radius:var(--radius-md);box-shadow:var(--shadow-lg);border:1px solid var(--gray-100);z-index:200;overflow:hidden;">
                    <a href="{{ url('/supervisor/profile') }}" style="display:flex;align-items:center;gap:10px;padding:10px 14px;font-size:.84rem;color:var(--gray-700);text-decoration:none;" onmouseover="this.style.background='var(--gray-50)'" onmouseout="this.style.background='transparent'">
                        <i class="fas fa-user-cog" style="width:16px;color:var(--gray-400);"></i> Profile
                    </a>
                    <div style="border-top:1px solid var(--gray-100);"></div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" style="display:flex;align-items:center;gap:10px;padding:10px 14px;font-size:.84rem;color:var(--color-danger);background:none;border:none;cursor:pointer;width:100%;" onmouseover="this.style.background='#fff1f2'" onmouseout="this.style.background='transparent'">
                            <i class="fas fa-sign-out-alt" style="width:16px;"></i> Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>
