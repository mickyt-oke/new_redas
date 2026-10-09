<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ in_array(auth()->user()?->user_category, ['desk_admin', 'directorate_admin']) ? 'Desk Admin Dashboard' : (auth()->user()?->role === 'directorate' ? 'Directorate Dashboard' : 'Officer Dashboard' ?? '') }} | NIS-REDAS</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/nis.png') }}">
    @include('partials.head-meta')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @laravelPWA

    <style>
        .auth-form-group {
            margin-bottom: 16px;
        }

        .pw-wrap {
            position: relative;
        }

        .pw-wrap .pw-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--gray-400);
            padding: 4px;
        }

        .pw-wrap .pw-toggle:hover {
            color: var(--gray-600);
        }

        .pw-wrap input {
            padding-right: 40px;
        }

        .inline-error {
            color: var(--color-danger);
            font-size: .78rem;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .helper-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="redas-dashboard">

@include('partials.preloader')

<!-- Mobile sidebar overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

@include('partials.sidebar')

<!-- ═══════ MAIN ═══════ -->
<div class="redas-main" id="redasMain">

    <!-- Topbar -->
    <header class="redas-topbar">
        <button class="topbar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        <div class="topbar-breadcrumb">
            <span>NIS-REDAS</span>
            <span class="separator"><i class="fas fa-chevron-right" style="font-size:.6rem;"></i></span>
            @if(in_array(auth()->user()?->user_category, ['desk_admin', 'directorate_admin']))
            <a href="{{ route('user.desk.home') }}" style="color:var(--gray-500);text-decoration:none;">Desk Admin Dashboard</a>
            @elseif(auth()->user()?->role === 'directorate')
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
                        <div class="notif-content"><div class="notif-title">Deadline Reminder</div><div class="notif-desc"></div></div>
                        <div class="notif-time">1d ago</div>
                    </div>
                    <div style="padding:10px;text-align:center;border-top:1px solid var(--gray-100);">
                        <a href="{{ route('user.notifications') }}" style="font-size:.78rem;color:var(--nis-600);font-weight:600;text-decoration:none;">View all</a>
                    </div>
                </div>
            </div>
            <div style="position:relative;">
                <button class="topbar-user" id="userMenuBtn" style="border:none;background:transparent;cursor:pointer;">
                    <div class="topbar-avatar">{{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 2)) }}</div>
                    <div class="topbar-user-info">
                        <div class="topbar-user-name">{{ auth()->user()?->name ?? 'Officer' }}</div>
                        <div class="topbar-user-role">{{ auth()->user()?->role === 'directorate' ? 'Directorate User' : 'State User' }}</div>
                    </div>
                    <i class="fas fa-chevron-down" style="font-size:.7rem;color:var(--gray-400);margin-left:4px;"></i>
                </button>
                <div id="userMenuDrop" onclick="event.stopPropagation()" style="display:none;position:absolute;right:0;top:calc(100% + 8px);width:190px;background:#fff;border-radius:var(--radius-md);box-shadow:var(--shadow-lg);border:1px solid var(--gray-100);z-index:200;overflow:hidden;">
                    @if(auth()->user()?->role !== 'user')
                    <a href="{{ route('user.profile') }}" style="display:flex;align-items:center;gap:10px;padding:10px 14px;font-size:.84rem;color:var(--gray-700);text-decoration:none;">
                        <i @class(['fas', 'fa-user-cog']) style="color:var(--gray-400);width:16px;"></i> Profile
                    </a>
                    @endif
                    <div style="border-top:1px solid var(--gray-100);"></div>
                    <form action="{{ route('lockscreen.lock') }}" method="POST" style="margin:0;">
                        @csrf
                        <button type="submit" id="lockSessionBtn" style="display:flex;align-items:center;gap:10px;padding:10px 14px;font-size:.84rem;color:var(--gray-700);background:none;border:none;cursor:pointer;width:100%;text-align:left;">
                            <i class="fas fa-lock" style="color:var(--gray-400);width:16px;"></i> Lock Session
                        </button>
                    </form>
                    <div style="border-top:1px solid var(--gray-100);"></div>
                    <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                        @csrf
                        <button type="submit" style="display:flex;align-items:center;gap:10px;padding:10px 14px;font-size:.84rem;color:var(--color-danger);background:none;border:none;cursor:pointer;width:100%;text-align:left;">
                            <i class="fas fa-sign-out-alt" style="width:16px;"></i> Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>
