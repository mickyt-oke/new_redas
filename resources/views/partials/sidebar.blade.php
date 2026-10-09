@php
    $user = auth()->user();
    $category = $user?->user_category;
    $isDeskAdmin = in_array($category, ['desk_admin', 'directorate_admin', 'cgis_desk_admin'], true);
@endphp

<aside class="redas-sidebar" id="redasSidebar">
    <a href="{{ $user?->homeRoute() ?? url('/') }}" class="sidebar-brand">
        <img src="{{ asset('assets/images/nis.png') }}" alt="NIS" class="sidebar-brand-logo">
        <div class="sidebar-brand-text">
            <span class="sidebar-brand-title">NIS&nbsp;REDAS</span>
            <span class="sidebar-brand-sub">{{ $user?->portalLabel() ?? 'Portal' }}</span>
        </div>
    </a>

    <nav class="sidebar-nav">
        @if($category === 'state_user')
            <div class="sidebar-section-label">Main Menu</div>

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

        @elseif($category === 'zonal_user')
            <div class="sidebar-section-label">Zonal User Menu</div>

            <a href="{{ route('user.zones.dashboard') }}" class="sidebar-link {{ request()->routeIs('user.zones.dashboard') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-tachometer-alt"></i></span>
                <span class="link-text">Dashboard</span>
            </a>

            <a href="{{ route('user.zones.returns.create') }}" class="sidebar-link {{ request()->routeIs('user.zones.returns.create') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-plus-circle"></i></span>
                <span class="link-text">Submit Zonal Return</span>
            </a>

            <a href="{{ route('user.zones.returns.index') }}" class="sidebar-link {{ request()->routeIs('user.zones.returns.index') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-inbox"></i></span>
                <span class="link-text">My Submissions</span>
            </a>

            <a href="{{ route('user.notifications') }}" class="sidebar-link {{ request()->is('user/notifications') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-bell"></i></span>
                <span class="link-text">Notifications</span>
            </a>

        @elseif($category === 'special_command_user')
            <div class="sidebar-section-label">Special Command Menu</div>

            <a href="{{ route('special-commands.dashboard') }}" class="sidebar-link {{ request()->routeIs('special-commands.dashboard') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-tachometer-alt"></i></span>
                <span class="link-text">Dashboard</span>
            </a>

            <a href="{{ route('special-commands.returns.create') }}" class="sidebar-link {{ request()->routeIs('special-commands.returns.create') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-plus-circle"></i></span>
                <span class="link-text">Submit Return</span>
            </a>

            <a href="{{ route('special-commands.returns.index') }}" class="sidebar-link {{ request()->routeIs('special-commands.returns.index') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-inbox"></i></span>
                <span class="link-text">My Submissions</span>
            </a>

            <a href="{{ route('user.notifications') }}" class="sidebar-link {{ request()->is('user/notifications') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-bell"></i></span>
                <span class="link-text">Notifications</span>
            </a>

        @elseif($category === 'directorate_user')
            <div class="sidebar-section-label">Directorate Menu</div>

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

            <a href="{{ route('user.notifications') }}" class="sidebar-link {{ request()->is('user/notifications') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-bell"></i></span>
                <span class="link-text">Notifications</span>
            </a>

            <a href="{{ route('user.archive') }}" class="sidebar-link {{ request()->routeIs('user.archive') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-archive"></i></span>
                <span class="link-text">Archive</span>
            </a>

        @elseif($category === 'cgis_unit_user')
            <div class="sidebar-section-label">CGIS Unit Menu</div>

            <a href="{{ route('user.cgis-units.dashboard') }}" class="sidebar-link {{ request()->routeIs('user.cgis-units.dashboard') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-tachometer-alt"></i></span>
                <span class="link-text">Dashboard</span>
            </a>

            @php $currentCgisSlug = $user?->cgisUnitSlug(); @endphp
            @if($currentCgisSlug)
                <a href="{{ route('user.cgis-units.show', ['slug' => $currentCgisSlug]) }}" class="sidebar-link {{ request()->routeIs('user.cgis-units.show') && request()->route('slug') === $currentCgisSlug ? 'active' : '' }}">
                    <span class="link-icon"><i class="fas fa-file-signature"></i></span>
                    <span class="link-text">My Unit Return</span>
                </a>
            @endif

            <a href="{{ route('user.notifications') }}" class="sidebar-link {{ request()->is('user/notifications') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-bell"></i></span>
                <span class="link-text">Notifications</span>
            </a>

            <a href="{{ route('user.archive') }}" class="sidebar-link {{ request()->routeIs('user.archive') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-archive"></i></span>
                <span class="link-text">Archive</span>
            </a>

        @elseif($isDeskAdmin)
            <div class="sidebar-section-label">{{ $category === 'directorate_admin' ? 'Directorate Admin Menu' : ($category === 'cgis_desk_admin' ? 'CGIS Desk Admin Menu' : 'Desk Admin Menu') }}</div>

            <a href="{{ route('user.desk.home') }}" class="sidebar-link {{ request()->routeIs('user.desk.home') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-th-large"></i></span>
                <span class="link-text">Review Dashboard</span>
            </a>

            <a href="{{ route('desk.admin.reports') }}" class="sidebar-link {{ request()->routeIs('desk.admin.reports') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-file-export"></i></span>
                <span class="link-text">Report Generation</span>
            </a>

            <a href="{{ route('user.archive') }}" class="sidebar-link {{ request()->routeIs('user.archive') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-archive"></i></span>
                <span class="link-text">Archived Documents</span>
            </a>

            <a href="{{ route('user.notifications') }}" class="sidebar-link {{ request()->is('user/notifications') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-bell"></i></span>
                <span class="link-text">Notifications</span>
            </a>

        @elseif($category === 'zonal_commander')
            <div class="sidebar-section-label">Zonal Command Menu</div>

            <a href="{{ route('user.zonal.home') }}" class="sidebar-link {{ request()->routeIs('user.zonal.home') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-th-large"></i></span>
                <span class="link-text">Zonal Dashboard</span>
            </a>

            <a href="{{ route('user.archive') }}" class="sidebar-link {{ request()->routeIs('user.archive') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-archive"></i></span>
                <span class="link-text">Archive</span>
            </a>

            <a href="{{ route('user.notifications') }}" class="sidebar-link {{ request()->is('user/notifications') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-bell"></i></span>
                <span class="link-text">Notifications</span>
            </a>

        @elseif(in_array($category, ['hq_admin', 'admin'], true))
            <div class="sidebar-section-label">HQ Administration</div>

            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-tachometer-alt"></i></span>
                <span class="link-text">Dashboard</span>
            </a>

            <a href="{{ route('admin.hq.returns') }}" class="sidebar-link {{ request()->is('admin/hq/returns*') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-clipboard-list"></i></span>
                <span class="link-text">Returns Register</span>
            </a>

            <a href="{{ route('admin.hq.archive') }}" class="sidebar-link {{ request()->routeIs('admin.hq.archive') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-archive"></i></span>
                <span class="link-text">Archive</span>
            </a>

            <a href="{{ route('admin.hq.analytics') }}" class="sidebar-link {{ request()->routeIs('admin.hq.analytics') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-chart-line"></i></span>
                <span class="link-text">Analytics</span>
            </a>

            <a href="{{ route('admin.hq.reports') }}" class="sidebar-link {{ request()->routeIs('admin.hq.reports') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-file-export"></i></span>
                <span class="link-text">Reports</span>
            </a>

            <a href="{{ route('admin.consolidation') }}" class="sidebar-link {{ request()->routeIs('admin.consolidation') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-layer-group"></i></span>
                <span class="link-text">Consolidation</span>
            </a>

            @if($category === 'hq_admin')
                <div class="sidebar-section-label">HQ Admin Actions</div>

                <a href="{{ route('user.desk.home') }}" class="sidebar-link {{ request()->routeIs('user.desk.home') ? 'active' : '' }}">
                    <span class="link-icon"><i class="fas fa-th-large"></i></span>
                    <span class="link-text">Review Dashboard</span>
                </a>

                <a href="{{ route('admin.submissions') }}" class="sidebar-link {{ request()->is('admin/submissions*') ? 'active' : '' }}">
                    <span class="link-icon"><i class="fas fa-tasks"></i></span>
                    <span class="link-text">Final Review Queue</span>
                </a>

                <a href="{{ route('admin.users') }}" class="sidebar-link {{ request()->is('admin/users*') ? 'active' : '' }}">
                    <span class="link-icon"><i class="fas fa-users-cog"></i></span>
                    <span class="link-text">User Management</span>
                </a>

                <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}">
                    <span class="link-icon"><i class="fas fa-cog"></i></span>
                    <span class="link-text">Settings</span>
                </a>

                <a href="{{ route('admin.audit-log.index') }}" class="sidebar-link {{ request()->routeIs('admin.audit-log.index') ? 'active' : '' }}">
                    <span class="link-icon"><i class="fas fa-history"></i></span>
                    <span class="link-text">Audit Log</span>
                </a>
            @endif

            <a href="{{ route('user.notifications') }}" class="sidebar-link {{ request()->is('user/notifications') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-bell"></i></span>
                <span class="link-text">Notifications</span>
            </a>

        @elseif($category === 'super_admin')
            <div class="sidebar-section-label">Executive Portal</div>

            <a href="{{ route('superadmin.dashboard') }}" class="sidebar-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-tachometer-alt"></i></span>
                <span class="link-text">Dashboard</span>
            </a>

            <a href="{{ route('superadmin.returns') }}" class="sidebar-link {{ request()->is('superadmin/returns*') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-clipboard-list"></i></span>
                <span class="link-text">Returns Register</span>
            </a>

            <a href="{{ route('user.notifications') }}" class="sidebar-link {{ request()->is('user/notifications') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-bell"></i></span>
                <span class="link-text">Notifications</span>
            </a>

        @else
            <div class="sidebar-section-label">Main Menu</div>

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
            </a>

            <a href="{{ route('user.notifications') }}" class="sidebar-link {{ request()->is('user/notifications') ? 'active' : '' }}">
                <span class="link-icon"><i class="fas fa-bell"></i></span>
                <span class="link-text">Notifications</span>
            </a>
        @endif
    </nav>
</aside>
