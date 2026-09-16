<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HQ Admin Dashboard') | NIS-REDAS</title>
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

<aside class="redas-sidebar" id="redasSidebar">
    <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
        <img src="{{ asset('assets/images/nis.png') }}" alt="NIS" class="sidebar-brand-logo">
        <div class="sidebar-brand-text">
            <span class="sidebar-brand-title">NIS&nbsp;REDAS</span>
            <span class="sidebar-brand-sub">HQ Admin Portal</span>
        </div>
    </a>

    <nav class="sidebar-nav">
        <div class="sidebar-section-label">Headquarters Menu</div>
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="link-icon"><i class="fas fa-th-large"></i></span>
            <span class="link-text">Dashboard</span>
        </a>
        <a href="{{ route('admin.hq.returns') }}" class="sidebar-link {{ request()->routeIs('admin.hq.returns*') ? 'active' : '' }}">
            <span class="link-icon"><i class="fas fa-folder-open"></i></span>
            <span class="link-text">All Returns</span>
        </a>
        <a href="{{ route('admin.hq.archive') }}" class="sidebar-link {{ request()->routeIs('admin.hq.archive') ? 'active' : '' }}">
            <span class="link-icon"><i class="fas fa-archive"></i></span>
            <span class="link-text">Archive</span>
        </a>
        <a href="{{ route('admin.hq.analytics') }}" class="sidebar-link {{ request()->routeIs('admin.hq.analytics') ? 'active' : '' }}">
            <span class="link-icon"><i class="fas fa-chart-pie"></i></span>
            <span class="link-text">Analytics</span>
        </a>
        <a href="{{ route('admin.hq.reports') }}" class="sidebar-link {{ request()->routeIs('admin.hq.reports*') ? 'active' : '' }}">
            <span class="link-icon"><i class="fas fa-file-excel"></i></span>
            <span class="link-text">Report Generation</span>
        </a>

        <div class="sidebar-section-label">Administration</div>
        <a href="{{ route('admin.users') }}" class="sidebar-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
            <span class="link-icon"><i class="fas fa-users-gear"></i></span>
            <span class="link-text">User Management</span>
        </a>
        <a href="{{ route('admin.audit-log.index') }}" class="sidebar-link {{ request()->routeIs('admin.audit-log*') ? 'active' : '' }}">
            <span class="link-icon"><i class="fas fa-clipboard-list"></i></span>
            <span class="link-text">Audit Log</span>
        </a>
        <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
            <span class="link-icon"><i class="fas fa-gear"></i></span>
            <span class="link-text">Settings</span>
        </a>
    </nav>
</aside>

<div class="redas-main" id="redasMain">
    <header class="redas-topbar">
        <button class="topbar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        <div class="topbar-breadcrumb">
            <span>NIS-REDAS</span>
            <span class="separator"><i class="fas fa-chevron-right" style="font-size:.6rem;"></i></span>
            <a href="{{ route('admin.dashboard') }}" style="color:var(--gray-500);text-decoration:none;">HQ Admin</a>
        </div>
        <div class="topbar-right">
            <div style="position:relative;">
                <button class="topbar-user" id="userMenuBtn" style="border:none;background:transparent;cursor:pointer;">
                    <div class="topbar-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'HQ', 0, 2)) }}</div>
                    <div class="topbar-user-info">
                        <div class="topbar-user-name">{{ auth()->user()->name ?? 'HQ Admin' }}</div>
                        <div class="topbar-user-role">Headquarters Administration</div>
                    </div>
                    <i class="fas fa-chevron-down" style="font-size:.7rem;color:var(--gray-400);margin-left:4px;"></i>
                </button>
                <div id="userMenuDrop" onclick="event.stopPropagation()" style="display:none;position:absolute;right:0;top:calc(100% + 8px);width:190px;background:#fff;border-radius:var(--radius-md);box-shadow:var(--shadow-lg);border:1px solid var(--gray-100);z-index:200;overflow:hidden;">
                    <a href="{{ route('user.profile') }}" style="display:flex;align-items:center;gap:10px;padding:10px 14px;font-size:.84rem;color:var(--gray-700);text-decoration:none;">
                        <i class="fas fa-user-cog" style="color:var(--gray-400);width:16px;"></i> Profile
                    </a>
                    <div style="border-top:1px solid var(--gray-100);"></div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" style="display:flex;align-items:center;gap:10px;padding:10px 14px;font-size:.84rem;color:var(--color-danger);background:none;border:none;cursor:pointer;width:100%;">
                            <i class="fas fa-sign-out-alt" style="width:16px;"></i> Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    @yield('content')

    @include('partials.footer')
</div>

<script>
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userMenuDrop = document.getElementById('userMenuDrop');
    if (userMenuBtn && userMenuDrop) {
        userMenuBtn.addEventListener('click', e => { e.stopPropagation(); userMenuDrop.style.display = userMenuDrop.style.display === 'block' ? 'none' : 'block'; });
        document.addEventListener('click', () => { userMenuDrop.style.display = 'none'; });
    }

    const sidebar = document.getElementById('redasSidebar');
    const main = document.getElementById('redasMain');
    const overlay = document.getElementById('sidebarOverlay');
    const toggle = document.getElementById('sidebarToggle');
    if (toggle) {
        toggle.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('active');
            } else {
                sidebar.classList.toggle('collapsed');
                main.classList.toggle('sidebar-collapsed');
            }
        });
    }
    if (overlay) {
        overlay.addEventListener('click', () => { sidebar.classList.remove('mobile-open'); overlay.classList.remove('active'); });
    }
</script>
@stack('scripts')
</body>
</html>
