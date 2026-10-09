<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>HQ Admin Dashboard | NIS-REDAS</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/nis.png') }}">
    @include('partials.head-meta')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @laravelPWA
</head>
<body class="redas-dashboard">

@include('partials.preloader')

<div class="sidebar-overlay" id="sidebarOverlay"></div>

@include('partials.admin-sidebar')

<!-- ════════════════════ MAIN ════════════════════ -->
<div class="redas-main" id="redasMain">

    <!-- ─── Topbar ─── -->
    <header class="redas-topbar">
        <button class="topbar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
            <i class="fas fa-bars"></i>
        </button>

        <div class="topbar-breadcrumb">
            <span>NIS-REDAS</span>
            <span class="separator"><i class="fas fa-chevron-right" style="font-size:.6rem;"></i></span>
            <span class="current">HQ National Dashboard</span>
        </div>

        <div class="topbar-right">
            <!-- System status -->
            <span style="background:#dcfce7;color:#15803d;border:1px solid #86efac;padding:5px 12px;border-radius:var(--radius-full);font-size:.72rem;font-weight:700;display:flex;align-items:center;gap:6px;">
                <span style="width:7px;height:7px;background:#15803d;border-radius:50%;animation:blink 2s infinite;display:inline-block;"></span>
                All Systems Operational
            </span>

            <!-- Notifications -->
            <div style="position:relative;">
                <button class="topbar-icon-btn" id="notifBtn" aria-label="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="notif-dot"></span>
                </button>
                <div id="notifPanel" style="display:none;position:absolute;right:0;top:calc(100% + 8px);width:320px;background:white;border-radius:var(--radius-lg);box-shadow:var(--shadow-xl);border:1px solid var(--gray-200);z-index:200;overflow:hidden;">
                    <div style="padding:12px 16px;font-size:.82rem;font-weight:700;color:var(--gray-800);border-bottom:1px solid var(--gray-100);display:flex;justify-content:space-between;">
                        <span>System Notifications</span>
                        <span style="color:var(--nis-600);cursor:pointer;font-weight:500;font-size:.76rem;">Mark all read</span>
                    </div>
                    @foreach([
                        ['warning','fas fa-exclamation-triangle', '15 formations overdue',     'Abuja, Kano, Lagos and 12 others have not submitted May returns.', '1h ago'],
                        ['info',   'fas fa-user-plus',            'New user registration',      '3 new officer accounts pending admin approval.', '3h ago'],
                        ['success','fas fa-database',             'Backup completed',           'Daily database backup completed successfully (2.3 GB).', '6h ago'],
                        ['danger', 'fas fa-shield-alt',           'Login anomaly detected',     '7 failed login attempts from IP 102.89.x.x — account locked.', '8h ago'],
                    ] as [$type, $icon, $title, $desc, $time])
                    <div class="notif-item unread">
                        <div class="notif-icon" style="background:{{ $type === 'danger' ? '#fee2e2' : ($type === 'warning' ? '#fef9c3' : ($type === 'success' ? '#dcfce7' : '#dbeafe')) }};color:{{ $type === 'danger' ? '#dc2626' : ($type === 'warning' ? '#a16207' : ($type === 'success' ? '#15803d' : '#1d4ed8')) }};">
                            <i class="{{ $icon }}"></i>
                        </div>
                        <div class="notif-content">
                            <div class="notif-title">{{ $title }}</div>
                            <div class="notif-desc">{{ $desc }}</div>
                        </div>
                        <div class="notif-time">{{ $time }}</div>
                    </div>
                    @endforeach
                    <div style="padding:10px 16px;text-align:center;border-top:1px solid var(--gray-100);">
                        <a href="{{ route('user.notifications') }}" style="font-size:.78rem;color:var(--nis-600);font-weight:600;text-decoration:none;">View all notifications →</a>
                    </div>
                </div>
            </div>

            <!-- User menu -->
            <div style="position:relative;">
                <button class="topbar-user" id="userMenuBtn" style="border:none;background:transparent;cursor:pointer;">
                    <div class="topbar-avatar" style="background:var(--color-info);">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
                    </div>
                    <div class="topbar-user-info" style="text-align:left;">
                        <div class="topbar-user-name">{{ auth()->user()->name ?? 'Administrator' }}</div>
                        <div class="topbar-user-role">ICT Admin — HQ</div>
                    </div>
                    <i class="fas fa-chevron-down" style="font-size:.7rem;color:var(--gray-400);margin-left:4px;"></i>
                </button>
                <div id="userMenuDrop" style="display:none;position:absolute;right:0;top:calc(100% + 8px);width:210px;background:white;border-radius:var(--radius-md);box-shadow:var(--shadow-lg);border:1px solid var(--gray-100);z-index:200;overflow:hidden;">
                    <a href="{{ route('user.profile') }}" style="display:flex;align-items:center;gap:10px;padding:10px 14px;font-size:.84rem;color:var(--gray-700);text-decoration:none;" onmouseover="this.style.background='var(--gray-50)'" onmouseout="this.style.background='transparent'"><i class="fas fa-user-cog" style="width:16px;color:var(--gray-400);"></i> Profile</a>
                    @if(auth()->user()?->user_category === 'hq_admin')
                        <a href="{{ route('admin.users') }}" style="display:flex;align-items:center;gap:10px;padding:10px 14px;font-size:.84rem;color:var(--gray-700);text-decoration:none;" onmouseover="this.style.background='var(--gray-50)'" onmouseout="this.style.background='transparent'"><i class="fas fa-users-cog" style="width:16px;color:var(--gray-400);"></i> User Management</a>
                        <a href="{{ route('admin.audit-log.index') }}" style="display:flex;align-items:center;gap:10px;padding:10px 14px;font-size:.84rem;color:var(--gray-700);text-decoration:none;" onmouseover="this.style.background='var(--gray-50)'" onmouseout="this.style.background='transparent'"><i class="fas fa-shield-alt" style="width:16px;color:var(--gray-400);"></i> Audit Log</a>
                        <a href="{{ route('admin.settings.index') }}" style="display:flex;align-items:center;gap:10px;padding:10px 14px;font-size:.84rem;color:var(--gray-700);text-decoration:none;" onmouseover="this.style.background='var(--gray-50)'" onmouseout="this.style.background='transparent'"><i class="fas fa-cog" style="width:16px;color:var(--gray-400);"></i> System Settings</a>
                    @endif
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
