@include('partials.header')
    <main class="redas-content">

        <div class="page-header">
            <div>
                <h1 class="page-title">Directorate Dashboard</h1>
                <p class="page-subtitle">
                    Welcome, <strong>{{ auth()->user()->name ?? 'Officer' }}</strong> —
                    {{ now()->format('l, d F Y') }}
                </p>
            </div>
        </div>

        <!-- Deadline Alert -->
        <div style="background:linear-gradient(135deg,#fef3c7,#fde68a);border:1px solid #f59e0b;border-radius:var(--radius-md);padding:14px 18px;display:flex;align-items:center;gap:14px;margin-bottom:24px;" class="animate-fade-up">
            <div style="width:40px;height:40px;background:#f59e0b;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;color:white;flex-shrink:0;">
                <i class="fas fa-calendar-exclamation"></i>
            </div>
            <div style="flex:1;">
                <strong style="color:#92400e;font-size:.9rem;">Monthly Return Due — {{ now()->endOfMonth()->format('d F Y') }}</strong>
                <p style="color:#b45309;font-size:.8rem;margin:0;">Submit your {{ now()->format('F Y') }} directorate return before the deadline to avoid penalties.</p>
            </div>
        </div>

        <!-- Directorates Grid -->
        <div class="redas-card animate-fade-up delay-1">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:var(--purple-50);color:var(--purple-600);">
                        <i class="fas fa-building-columns"></i>
                    </div>
                    NIS Directorates — Select Your Directorate
                </div>
            </div>
            <div class="card-body">
                @php
                    $directorates = [
                        ['slug' => 'hrm',           'name' => 'Human Resources Management (HRM)', 'icon' => 'fas fa-users',        'desc' => 'Personnel strength, promotions, training and welfare.'],
                        ['slug' => 'prs',           'name' => 'Planning, Research & Statistics',  'icon' => 'fas fa-chart-bar',     'desc' => 'Strategic planning, research outputs and statistical data.'],
                        ['slug' => 'finance',       'name' => 'Finance',                          'icon' => 'fas fa-coins',         'desc' => 'Budgets, expenditure and financial returns.'],
                        ['slug' => 'investigation', 'name' => 'Investigation',                    'icon' => 'fas fa-search',        'desc' => 'Cases investigated, arrests and prosecutions.'],
                        ['slug' => 'passport',      'name' => 'Passport',                         'icon' => 'fas fa-passport',      'desc' => 'Passport applications, issuances and renewals.'],
                        ['slug' => 'visa',          'name' => 'Visa',                             'icon' => 'fas fa-stamp',         'desc' => 'Visa applications, approvals and refusals.'],
                        ['slug' => 'migration',     'name' => 'Migration',                        'icon' => 'fas fa-globe-africa',  'desc' => 'Traveller statistics and migration trends.'],
                        ['slug' => 'border',        'name' => 'Border Management',                'icon' => 'fas fa-border-all',    'desc' => 'Border crossings, seizures and deployment.'],
                        ['slug' => 'ict',           'name' => 'ICT',                              'icon' => 'fas fa-laptop-code',   'desc' => 'Technology assets, systems and support tickets.'],
                        ['slug' => 'works-logistics','name' => 'Works & Logistics',               'icon' => 'fas fa-truck',         'desc' => 'Infrastructure, vehicles and logistics operations.'],
                    ];
                @endphp

                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:1.25rem;">
                    @foreach($directorates as $dir)
                    <a href="{{ route('user.directorates.show', $dir['slug']) }}"
                       style="display:flex;align-items:flex-start;gap:14px;padding:18px;border:1px solid var(--gray-200);border-radius:var(--radius-md);text-decoration:none;color:var(--gray-700);background:#fff;transition:.2s;"
                       onmouseover="this.style.borderColor='var(--nis-500)';this.style.transform='translateY(-2px)';this.style.boxShadow='var(--shadow-md)'"
                       onmouseout="this.style.borderColor='var(--gray-200)';this.style.transform='translateY(0)';this.style.boxShadow='none'">
                        <span style="width:42px;height:42px;border-radius:10px;background:var(--nis-50);color:var(--nis-600);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:1rem;">
                            <i class="{{ $dir['icon'] }}"></i>
                        </span>
                        <span style="flex:1;">
                            <strong style="font-size:.875rem;display:block;margin-bottom:3px;">{{ $dir['name'] }}</strong>
                            <span style="font-size:.78rem;color:var(--gray-500);">{{ $dir['desc'] }}</span>
                        </span>
                        <i class="fas fa-arrow-right" style="font-size:.75rem;color:var(--gray-400);margin-top:4px;flex-shrink:0;"></i>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

    </main>

@include('partials.footer')
