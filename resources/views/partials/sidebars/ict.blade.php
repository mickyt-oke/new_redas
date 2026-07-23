{{-- ==========================================================
    NIS REDAS
    ICT & Cybersecurity Directorate Sidebar
========================================================== --}}

<aside class="redas-sidebar" id="redasSidebar">

    {{-- ===========================
        Brand
    ============================ --}}
    <a href="{{ route('ict.dashboard') }}" class="sidebar-brand">
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

        <a href="{{ route('ict.report') }}" class="sidebar-link {{ request()->routeIs('ict.report') ? 'active' : '' }}">
            <span class="link-icon"><i class="fas fa-file-alt"></i></span>
            <span class="link-text">Annual Report</span>
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
 
         <a href="{{ route('ict.report') }}#staff-strength" class="sidebar-link">
             <span class="link-icon"><i class="fas fa-users"></i></span>
             <span class="link-text">Staff Strength</span>
         </a>
 
         <a href="{{ route('ict.report') }}#projects" class="sidebar-link">
             <span class="link-icon"><i class="fas fa-laptop-code"></i></span>
             <span class="link-text">Project/Programme Activities</span>
         </a>
 
         <a href="{{ route('ict.report') }}#incidents-hardware" class="sidebar-link">
             <span class="link-icon"><i class="fas fa-screwdriver-wrench"></i></span>
             <span class="link-text">Incident (Hardware)</span>
         </a>
 
         <a href="{{ route('ict.report') }}#incidents-software" class="sidebar-link">
             <span class="link-icon"><i class="fas fa-bug"></i></span>
             <span class="link-text">Incident (Software)</span>
         </a>
 
         <a href="{{ route('ict.report') }}#incidents-network" class="sidebar-link">
             <span class="link-icon"><i class="fas fa-network-wired"></i></span>
             <span class="link-text">Incident (Network)</span>
         </a>
 
         <a href="{{ route('ict.report') }}#incidents-cybersecurity" class="sidebar-link">
             <span class="link-icon"><i class="fas fa-shield-halved"></i></span>
             <span class="link-text">Incident (Cybersecurity)</span>
         </a>
 
         <a href="{{ route('ict.report') }}#incidents-power" class="sidebar-link">
             <span class="link-icon"><i class="fas fa-plug"></i></span>
             <span class="link-text">Incident (Power Supply System)</span>
         </a>
 
         <a href="{{ route('ict.report') }}#incidents-communication" class="sidebar-link">
             <span class="link-icon"><i class="fas fa-satellite-dish"></i></span>
             <span class="link-text">Incident (Communication)</span>
         </a>
 
         <a href="{{ route('ict.report') }}#incidents-surveillance" class="sidebar-link">
             <span class="link-icon"><i class="fas fa-video"></i></span>
             <span class="link-text">Incident (Surveillance)</span>
         </a>
 
         <a href="{{ route('ict.report') }}#incidents-providers" class="sidebar-link">
             <span class="link-icon"><i class="fas fa-handshake"></i></span>
             <span class="link-text">Incident (Technical Services Providers)</span>
         </a>
 
         <a href="{{ route('ict.report') }}#hardware-maintenance" class="sidebar-link">
             <span class="link-icon"><i class="fas fa-tools"></i></span>
             <span class="link-text">Hardware Maintenance</span>
         </a>
 
         <a href="{{ route('ict.report') }}#software-data" class="sidebar-link">
             <span class="link-icon"><i class="fas fa-database"></i></span>
             <span class="link-text">Software & Data Management</span>
         </a>
 
         <a href="{{ route('ict.report') }}#cybersecurity-deployment" class="sidebar-link">
             <span class="link-icon"><i class="fas fa-shield-halved"></i></span>
             <span class="link-text">Cybersecurity Deployment</span>
         </a>
 
         <a href="{{ route('ict.report') }}#id-cards" class="sidebar-link">
             <span class="link-icon"><i class="fas fa-id-card"></i></span>
             <span class="link-text">E-Documentation / ID Card Activities</span>
         </a>
 
         <a href="{{ route('ict.report') }}#midas-deployment" class="sidebar-link">
             <span class="link-icon"><i class="fas fa-server"></i></span>
             <span class="link-text">MIDAS Deployment</span>
         </a>

        <hr class="sidebar-divider">

        {{-- ACCOUNT --}}
        <div class="sidebar-section-label">ACCOUNT</div>

        <a href="#" class="sidebar-link">
            <span class="link-icon"><i class="fas fa-user"></i></span>
            <span class="link-text">My Profile</span>
        </a>

        <a href="#" class="sidebar-link">
            <span class="link-icon"><i class="fas fa-gear"></i></span>
            <span class="link-text">Settings</span>
        </a>

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
            <div class="sidebar-user-avatar">
                {{ strtoupper(substr(auth()->user()->name ?? 'I',0,2)) }}
            </div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">
                    {{ auth()->user()->name }}
                </div>
                <div class="sidebar-user-role">
                    ICT Directorate Officer
                </div>
            </div>
        </div>
    </div>

</aside>
