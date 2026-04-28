<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Officer Dashboard | NIS-REDAS</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/nis.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="redas-dashboard">

<!-- Mobile sidebar overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ════════════════════════════════ SIDEBAR ════════════════════════════════ -->
<aside class="redas-sidebar" id="redasSidebar">

    <!-- Brand -->
    @if(auth()->user()?->role === 'directorate')
    <a href="{{ route('user.directorate.home') }}" class="sidebar-brand">
    @else
    <a href="{{ route('user.dashboard') }}" class="sidebar-brand">
    @endif
        <img src="{{ asset('assets/images/nis.png') }}" alt="NIS" class="sidebar-brand-logo">
        <div class="sidebar-brand-text">
            <span class="sidebar-brand-title">NIS&nbsp;REDAS</span>
            <span class="sidebar-brand-sub">{{ auth()->user()?->role === 'directorate' ? 'Directorate Portal' : 'State Officer Portal' }}</span>
        </div>
    </a>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <div class="sidebar-section-label">Main Menu</div>

        @if(auth()->user()?->role === 'directorate')
        {{-- Directorate user: only directorate pages --}}
        <a href="{{ route('user.directorate.home') }}" class="sidebar-link {{ request()->routeIs('user.directorate.home') ? 'active' : '' }}">
            <span class="link-icon"><i class="fas fa-building-columns"></i></span>
            <span class="link-text">Directorates</span>
        </a>
        @else
        {{-- State user (officer): full access --}}
        <a href="{{ route('user.dashboard') }}" class="sidebar-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
            <span class="link-icon"><i class="fas fa-tachometer-alt"></i></span>
            <span class="link-text">Dashboard</span>
        </a>

        <a href="{{ url('/user/returns/create') }}" class="sidebar-link {{ request()->is('user/returns/create') ? 'active' : '' }}">
            <span class="link-icon"><i class="fas fa-plus-circle"></i></span>
            <span class="link-text">Submit New Return</span>
        </a>

        <a href="{{ url('/user/submissions') }}" class="sidebar-link {{ request()->is('user/submissions') ? 'active' : '' }}">
            <span class="link-icon"><i class="fas fa-inbox"></i></span>
            <span class="link-text">My Submissions</span>
            <span class="link-badge">3</span>
        </a>

        <a href="{{ url('/user/notifications') }}" class="sidebar-link {{ request()->is('user/notifications') ? 'active' : '' }}">
            <span class="link-icon"><i class="fas fa-bell"></i></span>
            <span class="link-text">Notifications</span>
            <span class="link-badge danger">2</span>
        </a>

        <a href="{{ route('user.archive') }}" class="sidebar-link {{ request()->routeIs('user.archive') ? 'active' : '' }}">
            <span class="link-icon"><i class="fas fa-archive"></i></span>
            <span class="link-text">Archive</span>
        </a>

        <a href="{{ route('user.reports') }}" class="sidebar-link {{ request()->routeIs('user.reports') ? 'active' : '' }}">
            <span class="link-icon"><i class="fas fa-file-export"></i></span>
            <span class="link-text">Generate Report</span>
        </a>
        @endif
</aside>

<!-- ═══════ MAIN ═══════ -->
<div class="redas-main" id="redasMain">

    <!-- Topbar -->
    <header class="redas-topbar">
        <button class="topbar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        <div class="topbar-breadcrumb">
            <span>NIS-REDAS</span>
            <span class="separator"><i class="fas fa-chevron-right" style="font-size:.6rem;"></i></span>
            @if(auth()->user()?->role === 'directorate')
            <a href="{{ route('user.directorate.home') }}" style="color:var(--gray-500);text-decoration:none;">Directorates</a>
            @else
            <a href="{{ route('user.dashboard') }}" style="color:var(--gray-500);text-decoration:none;">Dashboard</a>
            @endif
        </div>
        <div class="topbar-right">
            <div style="position:relative;">
                <button class="topbar-icon-btn" id="notifBtn"><i class="fas fa-bell"></i><span class="notif-dot"></span></button>
                <div id="notifPanel" style="display:none;position:absolute;right:0;top:calc(100% + 8px);width:300px;background:#fff;border-radius:var(--radius-lg);box-shadow:var(--shadow-xl);border:1px solid var(--gray-200);z-index:200;overflow:hidden;">
                    <div style="padding:12px 16px;font-size:.82rem;font-weight:700;border-bottom:1px solid var(--gray-100);">Notifications</div>
                    <div class="notif-item unread">
                        <div class="notif-icon" style="background:#fef9c3;color:#a16207;"><i class="fas fa-clock"></i></div>
                        <div class="notif-content"><div class="notif-title">Deadline Reminder</div><div class="notif-desc">May return due in 5 days.</div></div>
                        <div class="notif-time">1d ago</div>
                    </div>
                    <div style="padding:10px;text-align:center;border-top:1px solid var(--gray-100);">
                        <a href="{{ url('/user/notifications') }}" style="font-size:.78rem;color:var(--nis-600);font-weight:600;text-decoration:none;">View all</a>
                    </div>
                </div>
            </div>
            <div style="position:relative;">
                <button class="topbar-user" id="userMenuBtn" style="border:none;background:transparent;cursor:pointer;">
                    <div class="topbar-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}</div>
                    <div class="topbar-user-info">
                        <div class="topbar-user-name">{{ auth()->user()->name ?? 'Officer' }}</div>
                        <div class="topbar-user-role">{{ auth()->user()?->role === 'directorate' ? 'Directorate User' : 'State User' }}</div>
                    </div>
                    <i class="fas fa-chevron-down" style="font-size:.7rem;color:var(--gray-400);margin-left:4px;"></i>
                </button>
                <div id="userMenuDrop" style="display:none;position:absolute;right:0;top:calc(100% + 8px);width:190px;background:#fff;border-radius:var(--radius-md);box-shadow:var(--shadow-lg);border:1px solid var(--gray-100);z-index:200;overflow:hidden;">
                    @if(auth()->user()?->role !== 'directorate')
                    <a href="{{ url('/user/profile') }}" style="display:flex;align-items:center;gap:10px;padding:10px 14px;font-size:.84rem;color:var(--gray-700);text-decoration:none;">
                        <i @class(['fas', 'fa-user-cog']) style="color:var(--gray-400);width:16px;"></i> Profile
                    </a>
                    @endif
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
