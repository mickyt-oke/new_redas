<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="NIS-REDAS — Nigeria Immigration Service Reporting Dashboard & Archiving System. Centralised digital platform for operational returns and document management.">
    <title>NIS-REDAS | Nigeria Immigration Service</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|inter:400,500,600,700,800" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/nis.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Inline reveal styles for scroll animations */
        .reveal { opacity: 0; transform: translateY(24px); transition: opacity 0.6s ease, transform 0.6s ease; }
        .reveal.visible { opacity: 1; transform: translateY(0); }
        .reveal-delay-1 { transition-delay: 0.1s; }
        .reveal-delay-2 { transition-delay: 0.2s; }
        .reveal-delay-3 { transition-delay: 0.3s; }
        .reveal-delay-4 { transition-delay: 0.4s; }
        .shake { animation: shake .5s ease; }
        @keyframes shake {
            0%,100%{transform:translateX(0)} 20%{transform:translateX(-6px)} 40%{transform:translateX(6px)} 60%{transform:translateX(-4px)} 80%{transform:translateX(4px)}
        }
        .preview-bar-fill { transition: width 1.2s cubic-bezier(0.4,0,0.2,1) 0.8s; }
    </style>
</head>
<body class="welcome-page">

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay"></div>

<!-- ─── Top Navigation ─── -->
<nav class="welcome-nav" id="welcomeNav">
    <div class="nav-container">
        <a href="{{ url('/') }}" class="nav-brand">
            <img src="{{ asset('assets/images/nis-logo.png') }}" alt="NIS Logo" class="nav-brand-logo">
            <div class="nav-brand-text">
                <span class="nav-brand-name">NIS&nbsp;&nbsp;REDAS</span>
                <span class="nav-brand-sub">Reporting Dashboard &amp; Archiving System</span>
            </div>
        </a>

        <div class="nav-links">
            <a href="#home"     class="nav-link-item">Home</a>
            <a href="#features" class="nav-link-item">Features</a>
            <a href="#workflow" class="nav-link-item">Workflow</a>
            <a href="#roles"    class="nav-link-item">Access Levels</a>
            <a href="{{ route('login') }}" class="nav-cta">
                <i class="fas fa-sign-in-alt"></i> Access Portal
            </a>
        </div>

        <!-- Mobile hamburger -->
        <button class="topbar-toggle" id="mobileNavToggle" aria-label="Toggle navigation"
            style="background:rgba(255,255,255,0.1);border:none;width:40px;height:40px;border-radius:8px;color:white;cursor:pointer;display:none;">
            <i class="fas fa-bars"></i>
        </button>
    </div>
</nav>

<!-- ─── Hero ─── -->
<section class="welcome-hero" id="home">
    <div class="hero-pattern"></div>

    <div class="hero-container">
        <!-- Left: Text -->
        <div class="hero-text-side animate-fade-up">
            {{-- <div class="hero-eyebrow">
                <i class="fas fa-shield-alt"></i>
                Official NIS Digital Platform &mdash; Version 2.0
            </div> --}}

            <h1 class="hero-title">
                NIS Digitalised &amp; Centralized<br>
                <span class="accent">Reporting System</span><br>
            </h1>

            <p class="hero-desc">
                NIS-REDAS centralises operational data from all formations — HQ, Zonal Commands, State Commands, Area Commands, and Foreign Missions — into a single, secure, real-time digital platform.
            </p>

            <div class="hero-actions">
                <a href="{{ route('login') }}" class="btn-nis btn-primary-nis btn-lg">
                    <i class="fas fa-rocket"></i> Access Portal
                </a>
                <a href="#features" class="btn-nis btn-lg" style="background:rgba(255,255,255,0.1);color:white;border:1px solid rgba(255,255,255,0.2);">
                    <i class="fas fa-info-circle"></i> Learn More
                </a>
            </div>

            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="hero-stat-num" data-count="80">0</div>
                    <div class="hero-stat-label">State Formations</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-num" data-count="52">0</div>
                    <div class="hero-stat-label">Foreign Missions</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-num" data-count="8">0</div>
                    <div class="hero-stat-label">Zonal Commands</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-num" data-count="10">0</div>
                    <div class="hero-stat-label">Directorates</div>
                </div>
            </div>
        </div>

        <!-- Right: Dashboard Preview Card -->
        <div class="hero-visual animate-fade-right delay-2">
            <div class="hero-dashboard-preview">
                <div class="preview-header">
                    <span class="preview-dot red"></span>
                    <span class="preview-dot yellow"></span>
                    <span class="preview-dot green"></span>
                    <span class="preview-title">NIS-REDAS Dashboard v.1</span>
                </div>

                <div class="preview-stats">
                    <div class="preview-stat-card">
                        <div class="preview-stat-label">Total Submitted</div>
                        <div class="preview-stat-num green">847</div>
                    </div>
                    <div class="preview-stat-card">
                        <div class="preview-stat-label">Pending Review</div>
                        <div class="preview-stat-num gold">34</div>
                    </div>
                    <div class="preview-stat-card">
                        <div class="preview-stat-label">Approved</div>
                        <div class="preview-stat-num green">798</div>
                    </div>
                    <div class="preview-stat-card">
                        <div class="preview-stat-label">Overdue</div>
                        <div class="preview-stat-num red">15</div>
                    </div>
                </div>
                <!--
                <div class="preview-bar" style="margin-bottom:10px;">
                    <div class="preview-bar-label">
                        <span>Formation Compliance</span>
                        <span style="color:#4ade80;font-weight:700;">94%</span>
                    </div>
                    <div class="preview-bar-track">
                        <div class="preview-bar-fill" data-width="94%" style="width:0%;background:linear-gradient(90deg,#006633,#2d9e61);"></div>
                    </div>
                </div>
                <div class="preview-bar">
                    <div class="preview-bar-label">
                        <span>Archive Upload Progress</span>
                        <span style="color:#fbbf24;font-weight:700;">71%</span>
                    </div>
                    <div class="preview-bar-track">
                        <div class="preview-bar-fill" data-width="71%" style="width:0%;background:linear-gradient(90deg,#c5922a,#e4c06b);"></div>
                    </div>
                </div>
                -->
                <div style="margin-top:14px;padding-top:12px;border-top:1px solid rgba(255,255,255,0.1);display:flex;gap:8px;flex-wrap:wrap;">
                    <span style="background:rgba(74,222,128,0.15);color:#4ade80;padding:3px 10px;border-radius:999px;font-size:.68rem;font-weight:700;">● System Online</span>
                    <span style="background:rgba(197,146,42,0.15);color:#e4c06b;padding:3px 10px;border-radius:999px;font-size:.68rem;font-weight:700;">4 Pending Actions</span>
                    <span style="background:rgba(255,255,255,0.06);color:rgba(255,255,255,0.5);padding:3px 10px;border-radius:999px;font-size:.68rem;">Last sync: 2 min ago</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ─── Features ─── -->
<section class="welcome-features" id="features">
    <div class="section-container">
        <div class="section-header">
            <div class="section-eyebrow reveal">Platform Capabilities</div>
            <h2 class="section-title reveal reveal-delay-1">Everything the NIS Needs,<br>In One Place</h2>
            <p class="section-desc reveal reveal-delay-2">A unified platform covering the full operational lifecycle — from data entry at the state command to executive dashboards at HQ.</p>
        </div>

        <div class="features-grid">
            @foreach([
                ['fas fa-edit',         'Report Submission Portal',        'Structured data entry forms for monthly, quarterly, and annual returns across all operational domains — passport, border crossings, deportations, visa, and more.'],
                ['fas fa-project-diagram','Approval Workflow',             'Multi-tier review: Formation Officer → State/Zonal Supervisor → HQ Analyst. Each stage with automated notifications, comments, and audit trail.'],
                ['fas fa-chart-area',   'Analytics Dashboards',            'Real-time KPI dashboards for management — formation compliance heatmaps, trend charts, and one-click summary report generation.'],
                ['fas fa-archive',      'Digital Archive',                 'Secure, searchable repository for scanned passport and immigration case files from all 42 Nigerian Foreign Missions. Bulk upload with progress tracking.'],
                ['fas fa-users-cog',    'User & Role Management',          'Granular RBAC aligned to the NIS chain of command — from Data Entry Officer to CGIS — with bulk provisioning and audit log access.'],
                ['fas fa-shield-alt',   'Security & Compliance',           'AES-256 data at rest, TLS 1.2+ in transit, OWASP Top 10 protection, MFA for supervisors, and full NDPA 2023 compliance built-in.'],
                ['fas fa-bell',         'Deadline Reminders',              'Automated email and in-app notifications — 7 days and 2 days before submission deadlines — so no formation misses its return window.'],
                ['fas fa-file-export',  'Report Generation',               'Export data to Excel or PDF. Pre-formatted statistical bulletins. Custom report builder for PRS analysts with dimension selection and filters.'],
            ] as [$icon, $title, $desc])
            <div class="feature-card reveal" style="transition-delay:{{ $loop->index * 0.07 }}s;">
                <div class="feature-icon"><i class="{{ $icon }}"></i></div>
                <h3 class="feature-title">{{ $title }}</h3>
                <p class="feature-desc">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ─── Workflow ─── -->
<section style="padding:80px 0;background:var(--gray-50);" id="workflow">
    <div class="section-container">
        <div class="section-header">
            <div class="section-eyebrow reveal">How It Works</div>
            <h2 class="section-title reveal reveal-delay-1">A Clear Chain of Command,<br>Digitised</h2>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:0;position:relative;">
            @foreach([
                ['1', 'fas fa-pencil-alt', 'var(--nis-600)', 'Data Entry', 'State/Area Formation Officer logs into their role-specific portal and submits the monthly operational return using structured templates with built-in validation.'],
                ['2', 'fas fa-check-double','var(--gold-500)', 'Supervisor Review', 'Zonal or State Supervisor receives an instant notification, reviews the data, and either approves, returns with comments, or escalates for correction.'],
                ['3', 'fas fa-chart-bar',  'var(--color-info)', 'HQ Analysis', 'PRS Directorate Analyst at HQ aggregates approved data across all formations, runs trend analysis, and generates statistical bulletins.'],
                ['4', 'fas fa-user-tie',   'var(--color-pending)', 'Executive Reporting', 'CGIS and senior management access a read-only executive dashboard showing national KPIs, geographic compliance maps, and one-click PDF reports.'],
            ] as [$num, $icon, $color, $title, $desc])
            <div class="reveal" style="text-align:center;padding:32px 24px;position:relative;transition-delay:{{ $loop->index * 0.12 }}s;">
                @if(!$loop->last)
                <div style="position:absolute;top:52px;right:0;width:50%;height:2px;background:var(--gray-200);z-index:0;" class="d-none d-lg-block"></div>
                @endif
                <div style="width:64px;height:64px;border-radius:50%;background:{{ $color }};color:white;display:flex;align-items:center;justify-content:center;font-size:1.4rem;margin:0 auto 16px;position:relative;z-index:1;box-shadow:0 8px 24px rgba(0,0,0,0.12);">
                    <i class="{{ $icon }}"></i>
                </div>
                <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--gray-400);margin-bottom:4px;">Step {{ $num }}</div>
                <h3 style="font-size:1rem;font-weight:700;color:var(--gray-800);margin-bottom:10px;">{{ $title }}</h3>
                <p style="font-size:.82rem;color:var(--gray-500);line-height:1.6;">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ─── Access Levels ─── -->
<section class="welcome-roles" id="roles">
    <div class="section-container">
        <div class="section-header">
            <div class="section-eyebrow reveal" style="color:var(--gold-300);">Role-Based Access</div>
            <h2 class="section-title reveal reveal-delay-1" style="color:white;">The Right Tools for Every Level</h2>
            <p class="section-desc reveal reveal-delay-2" style="color:rgba(255,255,255,0.6);">
                REDAS adapts to your NIS rank and formation — from field data entry to national executive oversight.
            </p>
        </div>

        <div class="roles-grid">
            @foreach([
                ['fas fa-edit',       'Data Entry Officer', 'State / Area Command', 'Submit operational returns, upload supporting documents, track submission status, receive deadline reminders.'],
                ['fas fa-tasks',      'Supervisor',         'State / Zonal Command','Review and approve officer submissions within your zone, view formation compliance, escalate to HQ.'],
                ['fas fa-chart-pie',  'PRS Analyst',        'HQ — PRS Directorate', 'Aggregate national data, run trend analytics, configure templates, generate statistical bulletins and exports.'],
                ['fas fa-users-cog',  'ICT Admin',          'HQ — ICT Directorate', 'User management, audit log access, system health monitoring, bulk provisioning, and security incident reports.'],
                ['fas fa-user-tie',   'Executive',          'CGIS / DCGs / Directors','Read-only national KPI dashboard, geographic visualisations, one-click summary reports — mobile friendly.'],
                ['fas fa-archive',    'Archive Officer',    'Foreign Missions',     'Bulk upload scanned passport and immigration case files, index by applicant metadata, track upload progress.'],
            ] as [$icon, $role, $level, $desc])
            <div class="role-card reveal" style="transition-delay:{{ $loop->index * 0.07 }}s;">
                <div class="role-icon"><i class="{{ $icon }}"></i></div>
                <h3 class="role-title">{{ $role }}</h3>
                <div style="font-size:.68rem;color:var(--gold-400);font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;">{{ $level }}</div>
                <p class="role-desc">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ─── CTA Banner ─── -->
<section style="background:white;padding:60px 0;text-align:center;">
    <div class="section-container">
        <div class="reveal">
            <img src="{{ asset('assets/images/nis.png') }}" alt="NIS" style="height:64px;margin-bottom:20px;filter:drop-shadow(0 4px 12px rgba(0,0,0,0.1));">
            <h2 style="font-size:clamp(1.4rem,3vw,2rem);font-weight:800;color:var(--gray-900);margin-bottom:12px;">
                Ready to Modernise NIS Reporting?
            </h2>
            <p style="color:var(--gray-500);font-size:1rem;margin-bottom:28px;max-width:480px;margin-left:auto;margin-right:auto;">
                Log in with your NIS service number to access your role-specific dashboard.
            </p>
            <a href="{{ route('login') }}" class="btn-nis btn-primary-nis btn-lg" style="margin:0 auto;">
                <i class="fas fa-sign-in-alt"></i> Access Your Dashboard
            </a>
        </div>
    </div>
</section>

<!-- ─── Footer ─── -->
<footer class="welcome-footer">
    <div class="footer-container">
        <div class="footer-brand">
            <strong>Nigeria Immigration Service</strong><br>
            Service Headquarters, Airport Road, Sauka, Abuja — FCT
        </div>
        <div style="text-align:center;">
            <div style="display:flex;gap:16px;justify-content:center;margin-bottom:8px;">
                <a href="https://www.immigration.gov.ng" target="_blank" rel="noopener" style="color:rgba(255,255,255,0.5);font-size:.78rem;text-decoration:none;" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,0.5)'">
                    <i class="fas fa-globe me-1"></i>immigration.gov.ng
                </a>
                <span style="color:rgba(255,255,255,0.2);">|</span>
                <span style="color:rgba(255,255,255,0.5);font-size:.78rem;"><i class="fas fa-lock me-1"></i>RESTRICTED — Internal Use</span>
            </div>
        </div>
        <div class="footer-copy">
            &copy; {{ date('Y') }} Nigeria Immigration Service. All rights reserved.<br>
            NIS-REDAS v2.0 — ICT Directorate
        </div>
    </div>
</footer>

<script>
    /* Mobile nav toggle */
    const mobileToggle = document.getElementById('mobileNavToggle');
    const navLinks = document.querySelector('.nav-links');
    if (mobileToggle && navLinks) {
        mobileToggle.addEventListener('click', () => {
            const isOpen = navLinks.style.display === 'flex';
            navLinks.style.cssText = isOpen
                ? ''
                : 'display:flex;flex-direction:column;position:absolute;top:70px;left:0;right:0;background:#003d1a;padding:16px 24px;gap:4px;z-index:999;';
        });
    }

    /* Show mobile toggle on small screens */
    function checkMobile() {
        if (mobileToggle) mobileToggle.style.display = window.innerWidth <= 768 ? 'flex' : 'none';
        if (navLinks && window.innerWidth > 768) navLinks.style.cssText = '';
    }
    checkMobile();
    window.addEventListener('resize', checkMobile);
</script>
</body>
</html>
