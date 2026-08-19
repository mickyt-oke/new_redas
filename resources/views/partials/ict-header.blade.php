@php
    $unreadNotifications = \App\Models\UserNotification::where('user_id', auth()->id())
        ->where('is_read', false)
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();
    $unreadCount = $unreadNotifications->count();
@endphp
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
            <button class="topbar-icon-btn" id="notifBtn" style="position:relative;">
                <i class="fas fa-bell"></i>
                @if($unreadCount > 0)
                    <span class="notif-dot" style="position:absolute;top:2px;right:2px;width:8px;height:8px;background:red;border-radius:50%;"></span>
                @endif
            </button>

            <div id="notifPanel" onclick="event.stopPropagation()" style="display:none;position:absolute;right:0;top:calc(100% + 8px);width:320px;background:white;border-radius:var(--radius-lg);box-shadow:var(--shadow-xl);border:1px solid var(--gray-200);z-index:9999;overflow:hidden;">
                <div style="padding:12px 16px;font-size:.82rem;font-weight:700;color:var(--gray-800);border-bottom:1px solid var(--gray-100);display:flex;justify-content:space-between;align-items:center;">
                    <span>Notifications</span>
                    <span style="color:var(--nis-600);cursor:pointer;font-weight:500;font-size:.76rem;" onclick="markAllNotificationsAsRead()">Mark all read</span>
                </div>
                <div id="notifListContainer" style="max-height: 240px; overflow-y: auto;">
                    @forelse($unreadNotifications as $n)
                        <div class="notif-item unread" style="padding:10px 16px;border-bottom:1px solid var(--gray-100);display:flex;gap:12px;cursor:pointer;" onclick="window.location.href='{{ $n->action_url ?? '#' }}'">
                            <div class="notif-icon" style="background:#dbeafe;color:#1d4ed8;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="notif-content" style="flex:1;">
                                <div class="notif-title" style="font-size:0.8rem;font-weight:700;color:var(--gray-800);line-height:1.2;">{{ $n->title }}</div>
                                <div class="notif-desc" style="font-size:0.72rem;color:var(--gray-500);margin-top:2px;line-height:1.2;">{{ $n->description }}</div>
                            </div>
                        </div>
                    @empty
                        <div style="padding:20px;text-align:center;color:var(--gray-400);font-size:0.8rem;">No unread notifications</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div style="position:relative;">
            <button class="topbar-user" id="userMenuBtn" style="border:none;background:transparent;cursor:pointer;">
                <div class="topbar-avatar" style="padding:0;overflow:hidden;display:flex;align-items:center;justify-content:center;">
                    @if(auth()->user()->profile_picture)
                        <img src="/storage/{{ auth()->user()->profile_picture }}" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                    @endif
                </div>
                <div class="topbar-user-info" style="text-align:left;">
                    <div class="topbar-user-name">
                        {{ auth()->user()->name ?? 'User' }}
                    </div>
                    <div class="topbar-user-role">
                        {{ auth()->user()->user_category === 'directorate_admin' ? 'ICT & Cybersecurity Admin' : 'ICT & Cybersecurity Desk Officer' }}
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
                <a href="{{ route('user.profile') }}" style="display:flex;align-items:center;gap:10px;padding:12px 14px;font-size:.84rem;font-weight:600;color:var(--gray-700);text-decoration:none;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                    <i class="fas fa-user" style="width:16px;"></i> My Profile
                </a>
                <a href="{{ route('user.profile') }}#settings" style="display:flex;align-items:center;gap:10px;padding:12px 14px;font-size:.84rem;font-weight:600;color:var(--gray-700);text-decoration:none;border-bottom:1px solid var(--gray-100);" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                    <i class="fas fa-gear" style="width:16px;"></i> Settings
                </a>
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

<script>
function markAllNotificationsAsRead() {
    fetch('/user/notifications/mark-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    }).then(() => {
        const dot = document.querySelector('#notifBtn .notif-dot');
        if (dot) dot.remove();
        const container = document.getElementById('notifListContainer');
        if (container) {
            container.innerHTML = '<div style="padding:20px;text-align:center;color:var(--gray-400);font-size:0.8rem;">No unread notifications</div>';
        }
    });
}
document.getElementById('userMenuBtn')?.addEventListener('click', (e) => {
    e.stopImmediatePropagation();
    e.stopPropagation();
    const u = document.getElementById('userMenuDrop');
    if (u) {
        u.style.display = u.style.display === 'block' ? 'none' : 'block';
    }
    const n = document.getElementById('notifPanel');
    if (n) n.style.display = 'none';
});
document.getElementById('notifBtn')?.addEventListener('click', (e) => {
    e.stopImmediatePropagation();
    e.stopPropagation();
    const n = document.getElementById('notifPanel');
    if (n) {
        n.style.display = n.style.display === 'block' ? 'none' : 'block';
    }
    const u = document.getElementById('userMenuDrop');
    if (u) u.style.display = 'none';
});
document.addEventListener('click', () => {
    const u = document.getElementById('userMenuDrop');
    if (u) u.style.display = 'none';
    const n = document.getElementById('notifPanel');
    if (n) n.style.display = 'none';
});
</script>
<main class="redas-content">
