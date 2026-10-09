@php
    $user = auth()->user();
    $isHqAdmin = $user?->user_category === 'hq_admin';
@endphp

<aside class="redas-sidebar" id="redasSidebar">
    <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
        <img src="{{ asset('assets/images/nis.png') }}" alt="NIS" class="sidebar-brand-logo">
        <div class="sidebar-brand-text">
            <span class="sidebar-brand-title">NIS&nbsp;REDAS</span>
            <span class="sidebar-brand-sub">{{ $isHqAdmin ? 'HQ Admin Portal' : 'HQ Administration' }}</span>
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

        <a href="{{ route('admin.consolidation') }}" class="sidebar-link {{ request()->routeIs('admin.consolidation') ? 'active' : '' }}">
            <span class="link-icon"><i class="fas fa-layer-group"></i></span>
            <span class="link-text">Consolidation</span>
        </a>

        @if($isHqAdmin)
            <a href="{{ route('admin.submissions') }}" class="sidebar-link {{ request()->routeIs('admin.submissions') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-clipboard-check"></i></span>
                <span class="link-text">Review Queue</span>
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
        @endif
    </nav>
</aside>
