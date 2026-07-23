<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ICT & Cybersecurity Directorate | REDAS</title>
    <link rel="icon" href="{{ asset('assets/images/nis.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet">
    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>
<body class="redas-dashboard">

<div class="sidebar-overlay" id="sidebarOverlay"></div>

@include('partials.sidebars.ict')

<div class="redas-main" id="redasMain">

<header class="redas-topbar">
    <button class="topbar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <div class="topbar-breadcrumb">
        <span>NIS REDAS</span>
        <span class="separator"><i class="fas fa-chevron-right"></i></span>
        <span>ICT & Cybersecurity Directorate</span>
    </div>

    <div class="topbar-right">
        <div style="position:relative;">
            <button class="topbar-icon-btn">
                <i class="fas fa-bell"></i>
                <span class="notif-dot"></span>
            </button>
        </div>

        <div style="position:relative;">
            <button class="topbar-user" id="userMenuBtn" onclick="const d=document.getElementById('userMenuDrop');if(d){const show=d.style.display==='none'||!d.style.display;d.style.display=show?'block':'none';d.classList.toggle('open',show);}event.stopPropagation();" style="border:none;background:transparent;cursor:pointer;">
                <div class="topbar-avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                </div>
                <div class="topbar-user-info" style="text-align:left;">
                    <div class="topbar-user-name">
                        {{ auth()->user()->name ?? 'User' }}
                    </div>
                    <div class="topbar-user-role">
                        ICT Directorate Officer
                    </div>
                </div>
                <i class="fas fa-chevron-down" style="font-size:.7rem;color:var(--gray-400);margin-left:4px;"></i>
            </button>

            <div id="userMenuDrop" onclick="event.stopPropagation()" style="display:none;position:absolute;right:0;top:calc(100% + 8px);width:210px;background:#fff;border-radius:var(--radius-md);box-shadow:var(--shadow-lg);border:1px solid var(--gray-200);z-index:9999;overflow:hidden;">
                <div style="padding:12px 14px;border-bottom:1px solid var(--gray-100);background:#f8fafc;">
                    <div style="font-weight:700;font-size:.84rem;color:var(--gray-800);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ auth()->user()->name ?? 'User' }}
                    </div>
                    <div style="font-size:.72rem;color:var(--gray-500);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ auth()->user()->email ?? '' }}
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" style="display:flex;align-items:center;gap:10px;padding:12px 14px;font-size:.84rem;font-weight:600;color:var(--color-danger);background:none;border:none;cursor:pointer;width:100%;text-align:left;" onmouseover="this.style.background='#fff1f2'" onmouseout="this.style.background='transparent'">
                        <i class="fas fa-sign-out-alt" style="width:16px;"></i> Sign Out / Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
<main class="redas-content">
