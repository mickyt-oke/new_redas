{{-- ==========================================================
    NIS REDAS
    Visa & Residence Directorate Sidebar
========================================================== --}}

<aside class="redas-sidebar" id="redasSidebar">

    {{-- ===========================
        Brand
    ============================ --}}

    <a href="{{ route('visa.dashboard') }}"
       class="sidebar-brand">

        <img
            src="{{ asset('assets/images/nis.png') }}"
            class="sidebar-brand-logo"
            alt="NIS">

        <div class="sidebar-brand-text">

            <span class="sidebar-brand-title">

                NIS REDAS

            </span>

            <span class="sidebar-brand-sub">

                Visa Directorate

            </span>

        </div>

    </a>


    {{-- ===========================
        Navigation
    ============================ --}}

    <nav class="sidebar-nav">


        {{-- MAIN MENU --}}

        <div class="sidebar-section-label">

            MAIN MENU

        </div>


        <a href="{{ route('visa.dashboard') }}"
           class="sidebar-link {{ request()->routeIs('visa.dashboard') ? 'active' : '' }}">

            <span class="link-icon">

                <i class="fas fa-chart-line"></i>

            </span>

            <span class="link-text">

                Dashboard

            </span>

        </a>


        <a href="{{ route('visa.report') }}"
           class="sidebar-link {{ request()->routeIs('visa.report') ? 'active' : '' }}">

            <span class="link-icon">

                <i class="fas fa-file-alt"></i>

            </span>

            <span class="link-text">

                Annual Report

            </span>

        </a>


        <a href="{{ route('visa.submissions') }}"
           class="sidebar-link {{ request()->routeIs('visa.submissions') ? 'active' : '' }}">

            <span class="link-icon">

                <i class="fas fa-folder-open"></i>

            </span>

            <span class="link-text">

                Submitted Reports

            </span>

        </a>





        <hr class="sidebar-divider">


        {{-- REPORTING SECTIONS --}}

        <div class="sidebar-section-label">

            REPORTING SECTIONS

        </div>


        <a href="{{ route('visa.report') }}#general-information"
           class="sidebar-link">

            <span class="link-icon">

                <i class="fas fa-circle-info"></i>

            </span>

            <span class="link-text">

                General Information

            </span>

        </a>


        <a href="{{ route('visa.report') }}#staff-strength"
           class="sidebar-link">

            <span class="link-icon">

                <i class="fas fa-users"></i>

            </span>

            <span class="link-text">

                Staff Strength

            </span>

        </a>


        <a href="{{ route('visa.report') }}#e-migrant"
           class="sidebar-link">

            <span class="link-icon">

                <i class="fas fa-globe-africa"></i>

            </span>

            <span class="link-text">

                e-Migrant Centre

            </span>

        </a>


        <a href="{{ route('visa.report') }}#quota"
           class="sidebar-link">

            <span class="link-icon">

                <i class="fas fa-user-shield"></i>

            </span>

            <span class="link-text">

                Quota Administration

            </span>

        </a>


        <a href="{{ route('visa.report') }}#residence"
           class="sidebar-link">

            <span class="link-icon">

                <i class="fas fa-id-card"></i>

            </span>

            <span class="link-text">

                Residence Permit

            </span>

        </a>


        <a href="{{ route('visa.report') }}#ftz"
           class="sidebar-link">

            <span class="link-icon">

                <i class="fas fa-industry"></i>

            </span>

            <span class="link-text">

                Free Trade Zone

            </span>

        </a>
                <a href="{{ route('visa.report') }}#cerpac"
           class="sidebar-link">

            <span class="link-icon">
                <i class="fas fa-address-card"></i>
            </span>

            <span class="link-text">
                CERPAC Production
            </span>

        </a>


        <a href="{{ route('visa.report') }}#visa-applications"
           class="sidebar-link">

            <span class="link-icon">
                <i class="fas fa-passport"></i>
            </span>

            <span class="link-text">
                e-visa applications(svv)
            </span>

        </a>


        <a href="{{ route('visa.report') }}#trv"
           class="sidebar-link">

            <span class="link-icon">
                <i class="fas fa-plane"></i>
            </span>

            <span class="link-text">
                Temporary Resident Visa (TRV)
            </span>

        </a>


        <a href="{{ route('visa.report') }}#prv"
           class="sidebar-link">

            <span class="link-icon">
                <i class="fas fa-stamp"></i>
            </span>

            <span class="link-text">
                Permanent Residence Visa (PRV)
            </span>

        </a>


        <a href="{{ route('visa.report') }}#etwp"
           class="sidebar-link">

            <span class="link-icon">
                <i class="fas fa-laptop"></i>
            </span>

            <span class="link-text">
                e-TWP
            </span>

        </a>


        <a href="{{ route('visa.report') }}#visa-summary"
           class="sidebar-link">

            <span class="link-icon">
                <i class="fas fa-clipboard-list"></i>
            </span>

            <span class="link-text">
                Visa Summary
            </span>

        </a>


        <a href="{{ route('visa.report') }}#ecowas"
           class="sidebar-link">

            <span class="link-icon">
                <i class="fas fa-flag"></i>
            </span>

            <span class="link-text">
                ECOWAS
            </span>

        </a>


        <a href="{{ route('visa.report') }}#african-affairs"
           class="sidebar-link">

            <span class="link-icon">
                <i class="fas fa-earth-africa"></i>
            </span>

            <span class="link-text">
                African Affairs
            </span>

        </a>


        <hr class="sidebar-divider">


        {{-- ACCOUNT --}}

        <div class="sidebar-section-label">

            ACCOUNT

        </div>


        <a href="#"
           class="sidebar-link">

            <span class="link-icon">

                <i class="fas fa-user"></i>

            </span>

            <span class="link-text">

                My Profile

            </span>

        </a>


        <a href="#"
           class="sidebar-link">

            <span class="link-icon">

                <i class="fas fa-gear"></i>

            </span>

            <span class="link-text">

                Settings

            </span>

        </a>


        <form action="{{ route('logout') }}"
              method="POST">

            @csrf

            <button type="submit"
                    class="sidebar-link"
                    style="width:100%;
                           border:none;
                           background:none;
                           cursor:pointer;
                           text-align:left;">

                <span class="link-icon">

                    <i class="fas fa-sign-out-alt"></i>

                </span>

                <span class="link-text">

                    Logout

                </span>

            </button>

        </form>

    </nav>

        {{-- ==========================================
        SIDEBAR FOOTER
    =========================================== --}}

    <div class="sidebar-footer">

        <div class="sidebar-user-card">

            <div class="sidebar-user-avatar">

                {{ strtoupper(substr(auth()->user()->name ?? 'V',0,2)) }}

            </div>

            <div class="sidebar-user-info">

                <div class="sidebar-user-name">

                    {{ auth()->user()->name }}

                </div>

                <div class="sidebar-user-role">

                    Visa Directorate Officer

                </div>

            </div>

        </div>

    </div>

</aside>