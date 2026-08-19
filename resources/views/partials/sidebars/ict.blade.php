{{-- ==========================================================
    NIS REDAS
    ICT & Cybersecurity Directorate Sidebar
========================================================== --}}

<aside class="redas-sidebar" id="redasSidebar">

    {{-- ===========================
        Brand
    ============================ --}}
    <a href="{{ auth()->user()->user_category === 'directorate_user' ? route('ict.submissions') : route('ict.dashboard') }}" class="sidebar-brand">
        <img src="{{ asset('assets/images/nis.png') }}" class="sidebar-brand-logo" alt="NIS">
        <div class="sidebar-brand-text">
            <span class="sidebar-brand-title">NIS REDAS</span>
            <span class="sidebar-brand-sub">ICT & Cyber Directorate</span>
        </div>
    </a>

    {{-- ===========================
        Navigation
    ============================ --}}
    <nav class="sidebar-nav">

        {{-- MAIN MENU --}}
        <div class="sidebar-section-label">MAIN MENU</div>

        <a href="{{ route('ict.dashboard') }}" class="sidebar-link {{ request()->routeIs('ict.dashboard') ? 'active' : '' }}">
            <span class="link-icon"><i class="fas fa-chart-line"></i></span>
            <span class="link-text">Dashboard</span>
        </a>

        <a href="{{ route('ict.submissions') }}" class="sidebar-link {{ request()->routeIs('ict.submissions') ? 'active' : '' }}">
            <span class="link-icon"><i class="fas fa-folder-open"></i></span>
            <span class="link-text">Submitted Reports</span>
        </a>


        <hr class="sidebar-divider">
 
         {{-- REPORTING SECTIONS --}}
         <div class="sidebar-section-label">REPORTING SECTIONS</div>
 
         <a href="{{ route('ict.report') }}#general-information" class="sidebar-link">
             <span class="link-icon"><i class="fas fa-circle-info"></i></span>
             <span class="link-text">General Information</span>
         </a>

        <hr class="sidebar-divider">

        {{-- ACCOUNT --}}
        <div class="sidebar-section-label">ACCOUNT</div>

        <a href="{{ route('user.profile') }}" class="sidebar-link {{ request()->routeIs('user.profile') && !str_contains(request()->fullUrl(), '#settings') ? 'active' : '' }}">
            <span class="link-icon"><i class="fas fa-user"></i></span>
            <span class="link-text">My Profile</span>
        </a>

        <a href="{{ route('user.profile') }}#settings" class="sidebar-link">
            <span class="link-icon"><i class="fas fa-gear"></i></span>
            <span class="link-text">Settings</span>
        </a>

        @if(auth()->user()->user_category === 'directorate_admin')
        <a href="{{ route('admin.users') }}"
           class="sidebar-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
            <span class="link-icon"><i class="fas fa-users-cog"></i></span>
            <span class="link-text">User Management</span>
        </a>
        @endif

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="sidebar-link" style="width:100%; border:none; background:none; cursor:pointer; text-align:left;">
                <span class="link-icon"><i class="fas fa-sign-out-alt"></i></span>
                <span class="link-text">Logout</span>
            </button>
        </form>

    </nav>

    {{-- SIDEBAR FOOTER --}}
    <div class="sidebar-footer">
        <div class="sidebar-user-card">
            <div class="sidebar-user-avatar" style="padding:0;overflow:hidden;display:flex;align-items:center;justify-content:center;">
                @if(auth()->user()->profile_picture)
                    <img src="/storage/{{ auth()->user()->profile_picture }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                    {{ strtoupper(substr(auth()->user()->name ?? 'I',0,2)) }}
                @endif
            </div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">
                    {{ auth()->user()->name }}
                </div>
                <div class="sidebar-user-role">
                    {{ auth()->user()->user_category === 'directorate_admin' ? 'ICT & Cybersecurity Admin' : 'ICT & Cybersecurity Desk Officer' }}
                </div>
            </div>
        </div>
    </div>

</aside>
