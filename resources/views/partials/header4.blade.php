<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $user = auth()->user();
        $isCgisUnitUser = $user?->user_category === 'cgis_unit_user';
        $isDeskAdmin = in_array($user?->user_category, ['desk_admin', 'directorate_admin']);
        $isDirectorateUser = $user?->role === 'directorate';
    @endphp
    <title>
        @if($isCgisUnitUser)
            CGIS Unit Dashboard
        @elseif($isDeskAdmin)
            Desk Admin Dashboard
        @elseif($isDirectorateUser)
            Directorate Dashboard
        @else
            Officer Dashboard
        @endif
        | NIS-REDAS
    </title>
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
    @php
        $user = auth()->user();
        $currentCgisSlug = $user?->cgisUnitSlug();
        $cgisUnits = [
            'actu' => ['label' => 'ACTU', 'icon' => 'fas fa-shield-halved'],
            'epms' => ['label' => 'EPMS', 'icon' => 'fas fa-chart-line'],
            'hostmanship' => ['label' => 'Hostmanship', 'icon' => 'fas fa-people-arrows'],
            'pro-media' => ['label' => 'Pro-Media', 'icon' => 'fas fa-bullhorn'],
            'protocol' => ['label' => 'Protocol', 'icon' => 'fas fa-handshake-angle'],
            'provost' => ['label' => 'Provost', 'icon' => 'fas fa-user-shield'],
            'servicom' => ['label' => 'SERVICOM', 'icon' => 'fas fa-handshake'],
        ];

        $brandRoute = route('user.dashboard');
        $brandText = 'State Officer Portal';

        if ($user?->user_category === 'cgis_unit_user') {
            $brandRoute = route('user.cgis-units.dashboard');
            $brandText = 'CGIS Unit Portal';
        } elseif (in_array($user?->user_category, ['desk_admin', 'directorate_admin'])) {
            $brandRoute = route('user.desk.home');
            $brandText = $user?->user_category === 'directorate_admin' ? 'Directorate Admin Portal' : 'Desk Admin Portal';
        } elseif ($user?->user_category === 'zonal_commander') {
            $brandRoute = route('user.zonal.home');
            $brandText = 'Zonal Command Portal';
        } elseif ($user?->role === 'directorate') {
            $brandRoute = route('user.directorates.dashboard');
            $brandText = 'Directorate Portal';
        }
    @endphp

    <a href="{{ $brandRoute }}" class="sidebar-brand">
        <img src="{{ asset('assets/images/nis.png') }}" alt="NIS" class="sidebar-brand-logo">
        <div class="sidebar-brand-text">
            <span class="sidebar-brand-title">NIS&nbsp;REDAS</span>
            <span class="sidebar-brand-sub">{{ $brandText }}</span>
        </div>
    </a>

    <nav class="sidebar-nav">
        @if($user?->user_category === 'cgis_unit_user')
            <div class="sidebar-section-label">CGIS Unit Menu</div>

            <a href="{{ route('user.cgis-units.dashboard') }}" class="sidebar-link {{ request()->routeIs('user.cgis-units.dashboard') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-tachometer-alt"></i></span>
                <span class="link-text">Dashboard</span>
            </a>

            @if($currentCgisSlug)
                <a href="{{ route('user.cgis-units.show', ['slug' => $currentCgisSlug]) }}" class="sidebar-link {{ request()->routeIs('user.cgis-units.show') && request()->route('slug') === $currentCgisSlug ? 'active' : '' }}">
                    <span class="link-icon"><i class="fas fa-file-signature"></i></span>
                    <span class="link-text">My Unit Return</span>
                </a>
            @endif

            {{-- @foreach($cgisUnits as $slug => $unit)
                <a href="{{ route('user.cgis-units.show', ['slug' => $slug]) }}" class="sidebar-link {{ request()->routeIs('user.cgis-units.show') && request()->route('slug') === $slug ? 'active' : '' }}">
                    <span class="link-icon"><i class="{{ $unit['icon'] }}"></i></span>
                    <span class="link-text">{{ $unit['label'] }} Unit</span>
                </a>
            @endforeach --}}

        @else
            <div class="sidebar-section-label">{{ in_array($user?->user_category, ['desk_admin', 'directorate_admin']) ? 'Desk Admin Menu' : 'Main Menu' }}</div>

            @if(in_array($user?->user_category, ['desk_admin', 'directorate_admin']))
                <a href="{{ route('user.desk.home') }}" class="sidebar-link {{ request()->routeIs('user.desk.home') ? 'active' : '' }}">
                    <span class="link-icon"><i class="fas fa-th-large"></i></span>
                    <span class="link-text">Review Dashboard</span>
                </a>

                <a href="{{ route('user.reports') }}" class="sidebar-link {{ request()->routeIs('user.reports') ? 'active' : '' }}">
                    <span class="link-icon"><i class="fas fa-file-export"></i></span>
                    <span class="link-text">Cumulative Reports</span>
                </a>

                <a href="{{ route('user.archive') }}" class="sidebar-link {{ request()->routeIs('user.archive') ? 'active' : '' }}">
                    <span class="link-icon"><i class="fas fa-archive"></i></span>
                    <span class="link-text">Archived Documents</span>
                </a>

                <a href="{{ route('user.submissions') }}" class="sidebar-link {{ request()->is('user/submissions') ? 'active' : '' }}">
                    <span class="link-icon"><i class="fas fa-inbox"></i></span>
                    <span class="link-text">Officer Submissions</span>
                </a>

                <a href="{{ route('user.notifications') }}" class="sidebar-link {{ request()->is('user/notifications') ? 'active' : '' }}">
                    <span class="link-icon"><i class="fas fa-bell"></i></span>
                    <span class="link-text">Notifications</span>
                </a>
            @else
                @if($user?->role === 'directorate')
                    <a href="{{ route('user.directorates.dashboard') }}" class="sidebar-link {{ request()->routeIs('user.directorates.dashboard') ? 'active' : '' }}">
                        <span class="link-icon"><i class="fas fa-tachometer-alt"></i></span>
                        <span class="link-text">Dashboard</span>
                    </a>

                    @php $userDirectorateSlug = $user?->directorateSlug(); @endphp
                    @if($userDirectorateSlug)
                        <a href="{{ route('user.directorates.show', ['slug' => $userDirectorateSlug]) }}" class="sidebar-link {{ request()->routeIs('user.directorates.show') ? 'active' : '' }}">
                            <span class="link-icon"><i class="fas fa-file-signature"></i></span>
                            <span class="link-text">Submit Return</span>
                        </a>
                    @endif
                @else
                    <a href="{{ route('user.dashboard') }}" class="sidebar-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                        <span class="link-icon"><i class="fas fa-tachometer-alt"></i></span>
                        <span class="link-text">Dashboard</span>
                    </a>

                    <a href="{{ route('user.returns.create') }}" class="sidebar-link {{ request()->is('user/returns/create') ? 'active' : '' }}">
                        <span class="link-icon"><i class="fas fa-plus-circle"></i></span>
                        <span class="link-text">Submit New Return</span>
                    </a>

                    <a href="{{ route('user.submissions') }}" class="sidebar-link {{ request()->is('user/submissions') ? 'active' : '' }}">
                        <span class="link-icon"><i class="fas fa-inbox"></i></span>
                        <span class="link-text">My Submissions</span>
                        <span class="link-badge"></span>
                    </a>

                    <a href="{{ route('user.notifications') }}" class="sidebar-link {{ request()->is('user/notifications') ? 'active' : '' }}">
                        <span class="link-icon"><i class="fas fa-bell"></i></span>
                        <span class="link-text">Notifications</span>
                        <span class="link-badge danger"></span>
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
            @endif
        @endif
    </nav>
</aside>

<div class="redas-main" id="redasMain">
    <header class="redas-topbar">
        <button class="topbar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        <div class="topbar-breadcrumb">
            <span>NIS-REDAS</span>
            <span class="separator"><i class="fas fa-chevron-right" style="font-size:.6rem;"></i></span>
            @if($user?->user_category === 'cgis_unit_user')
                <a href="{{ route('user.cgis-units.dashboard') }}" style="color:var(--gray-500);text-decoration:none;">CGIS Unit Dashboard</a>
            @elseif(in_array($user?->user_category, ['desk_admin', 'directorate_admin']))
                <a href="{{ route('user.desk.home') }}" style="color:var(--gray-500);text-decoration:none;">Desk Admin Dashboard</a>
            @elseif($user?->role === 'directorate')
                <a href="{{ route('user.directorates.dashboard') }}" style="color:var(--gray-500);text-decoration:none;">Directorate Dashboard</a>
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
                        <div class="notif-content"><div class="notif-title">Deadline Reminder</div><div class="notif-desc">Monthly return due in 5 days.</div></div>
                        <div class="notif-time">1d ago</div>
                    </div>
                    <div style="padding:10px;text-align:center;border-top:1px solid var(--gray-100);">
                        <a href="{{ route('user.notifications') }}" style="font-size:.78rem;color:var(--nis-600);font-weight:600;text-decoration:none;">View all</a>
                    </div>
                </div>
            </div>
            <div style="position:relative;">
                <button class="topbar-user" id="userMenuBtn" style="border:none;background:transparent;cursor:pointer;">
                    <div class="topbar-avatar">{{ strtoupper(substr($user->name ?? 'U', 0, 2)) }}</div>
                    <div class="topbar-user-info">
                        <div class="topbar-user-name">{{ $user->name ?? 'Officer' }}</div>
                        <div class="topbar-user-role">{{ $user?->user_category === 'cgis_unit_user' ? 'CGIS Unit User' : ($user?->role === 'directorate' ? 'Directorate User' : 'State User') }}</div>
                    </div>
                    <i class="fas fa-chevron-down" style="font-size:.7rem;color:var(--gray-400);margin-left:4px;"></i>
                </button>
                <div id="userMenuDrop" onclick="event.stopPropagation()" style="display:none;position:absolute;right:0;top:calc(100% + 8px);width:190px;background:#fff;border-radius:var(--radius-md);box-shadow:var(--shadow-lg);border:1px solid var(--gray-100);z-index:200;overflow:hidden;">
                    @if($user?->role !== 'user')
                        <a href="{{ route('user.profile') }}" style="display:flex;align-items:center;gap:10px;padding:10px 14px;font-size:.84rem;color:var(--gray-700);text-decoration:none;">
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
