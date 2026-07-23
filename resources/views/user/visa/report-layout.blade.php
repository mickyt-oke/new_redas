@include('partials.header')

<main class="redas-content">

    <div class="page-header">

        <div>

            <h1 class="page-title">

                <i class="fas fa-passport"></i>

                Visa & Residence Directorate

            </h1>

            <p class="page-subtitle">

                Annual Operational Reporting System

            </p>

        </div>

        <div style="display:flex;gap:12px;">

            <button
                type="button"
                class="btn-nis btn-ghost">

                <i class="fas fa-save"></i>

                Save Draft

            </button>

            <button
                type="submit"
                form="visaReportForm"
                class="btn-nis btn-primary-nis">

                <i class="fas fa-paper-plane"></i>

                Submit Report

            </button>

        </div>

    </div>

    <!-- Progress -->

    <div class="redas-card mb-4">

        <div class="card-body">

            <div
                style="
                display:flex;
                justify-content:space-between;
                align-items:center;
                margin-bottom:12px;">

                <strong>

                    Annual Report Progress

                </strong>

                <span id="reportProgressText">

                    0%

                </span>

            </div>

            <div
                style="
                width:100%;
                height:12px;
                background:#edf2f7;
                border-radius:999px;
                overflow:hidden;">

                <div

                    id="reportProgressBar"

                    style="
                    width:0%;
                    height:100%;
                    background:linear-gradient(90deg,#0B6B3A,#16a34a);
                    transition:.4s;">

                </div>

            </div>

        </div>

    </div>

    <div class="visa-layout">

        <!-- Sidebar -->

        <aside class="visa-sidebar">

            <div class="visa-sidebar-title">

                Reporting Sections

            </div>

            <nav class="visa-menu">

                <a
                    href="#general-information"
                    class="visa-menu-item active">

                    <i class="fas fa-file-alt"></i>

                    General Information

                </a>

                <a
                    href="#staff-strength"
                    class="visa-menu-item">

                    <i class="fas fa-users"></i>

                    Staff Strength

                </a>

                <a
                    href="#e-migrant"
                    class="visa-menu-item">

                    <i class="fas fa-globe-africa"></i>

                    e-Migrant Centre

                </a>

                <a
                    href="#quota"
                    class="visa-menu-item">

                    <i class="fas fa-user-check"></i>

                    Quota Administration

                </a>

                <a
                    href="#residence"
                    class="visa-menu-item">

                    <i class="fas fa-id-card"></i>

                    Residence Permit

                </a>

                <a
                    href="#ftz"
                    class="visa-menu-item">

                    <i class="fas fa-industry"></i>

                    Free Trade Zone

                </a>

                <a
                    href="#cerpac"
                    class="visa-menu-item">

                    <i class="fas fa-address-card"></i>

                    CERPAC

                </a>

                <a
                    href="#visa"
                    class="visa-menu-item">

                    <i class="fas fa-passport"></i>

                    Visa Applications

                </a>

                <a
                    href="#trv"
                    class="visa-menu-item">

                    <i class="fas fa-plane"></i>

                    TRV

                </a>

                <a
                    href="#prv"
                    class="visa-menu-item">

                    <i class="fas fa-stamp"></i>

                    PRV

                </a>

                <a
                    href="#etwp"
                    class="visa-menu-item">

                    <i class="fas fa-laptop"></i>

                    e-TWP

                </a>

                <a
                    href="#visa-summary"
                    class="visa-menu-item">

                    <i class="fas fa-clipboard-list"></i>

                    Visa Summary

                </a>

                <a
                    href="#ecowas"
                    class="visa-menu-item">

                    <i class="fas fa-flag"></i>

                    ECOWAS

                </a>

                <a
                    href="#african-affairs"
                    class="visa-menu-item">

                    <i class="fas fa-earth-africa"></i>

                    African Affairs

                </a>

            </nav>

        </aside>

        <!-- Main -->

        <section class="visa-workspace">

            <form
                id="visaReportForm"
                method="POST"
                action="{{ route('visa.store') }}">

                @csrf

                @include('user.visa.sections.general-information')

                @include('user.visa.sections.staff-strength')

                @include('user.visa.sections.e-migrant')

                @include('user.visa.sections.quota')

                @include('user.visa.sections.residence')

                @include('user.visa.sections.ftz')

                @include('user.visa.sections.cerpac')

                @include('user.visa.sections.visa')

                @include('user.visa.sections.trv')

                @include('user.visa.sections.prv')

                @include('user.visa.sections.etwp')

                @include('user.visa.sections.visa-counter')

                @include('user.visa.sections.ecowas')

                @include('user.visa.sections.african-affairs')

            </form>

        </section>

    </div>

</main>

@include('partials.footer')