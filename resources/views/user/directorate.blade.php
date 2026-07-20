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
                    <a href="{{ route('user.directorates.show', ['slug' => $dir['slug']]) }}"
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

        <!-- Directorate Performance Overview -->
        <div class="redas-card animate-fade-up delay-2" style="margin-top:24px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:var(--green-50);color:var(--green-600);">
                        <i class="fas fa-gauge-high"></i>
                    </div>
                    Directorate Performance Overview
                </div>
            </div>
            <div class="card-body">
                @php
                    $currentMonth = now()->format('F Y');
                    $performanceRows = [
                        ['slug' => 'hrm',            'name' => 'Human Resources Management (HRM)', 'submitted' => 1, 'expected' => 1, 'days_early' => 2],
                        ['slug' => 'prs',            'name' => 'Planning, Research & Statistics',  'submitted' => 1, 'expected' => 1, 'days_early' => 1],
                        ['slug' => 'finance',        'name' => 'Finance',                          'submitted' => 1, 'expected' => 1, 'days_early' => 0],
                        ['slug' => 'investigation',  'name' => 'Investigation',                    'submitted' => 0, 'expected' => 1, 'days_early' => -3],
                        ['slug' => 'passport',       'name' => 'Passport',                         'submitted' => 1, 'expected' => 1, 'days_early' => 4],
                        ['slug' => 'visa',           'name' => 'Visa',                             'submitted' => 0, 'expected' => 1, 'days_early' => -1],
                        ['slug' => 'migration',      'name' => 'Migration',                        'submitted' => 1, 'expected' => 1, 'days_early' => 2],
                        ['slug' => 'border',         'name' => 'Border Management',                'submitted' => 1, 'expected' => 1, 'days_early' => 1],
                        ['slug' => 'ict',            'name' => 'ICT',                              'submitted' => 0, 'expected' => 1, 'days_early' => -2],
                        ['slug' => 'works-logistics','name' => 'Works & Logistics',                'submitted' => 1, 'expected' => 1, 'days_early' => 0],
                    ];

                    $totalDirectorates = count($performanceRows);
                    $totalSubmitted = collect($performanceRows)->sum('submitted');
                    $totalExpected = collect($performanceRows)->sum('expected');
                    $totalPending = max($totalExpected - $totalSubmitted, 0);
                    $complianceRate = $totalExpected > 0 ? round(($totalSubmitted / $totalExpected) * 100, 1) : 0;

                    $onTimeCount = collect($performanceRows)->filter(fn($r) => $r['submitted'] === 1 && $r['days_early'] >= 0)->count();
                    $avgTimeliness = $totalSubmitted > 0
                        ? round(collect($performanceRows)->filter(fn($r) => $r['submitted'] === 1)->avg('days_early'), 1)
                        : 0;

                    $ranked = collect($performanceRows)->map(function ($r) {
                        $score = ($r['expected'] > 0 ? ($r['submitted'] / $r['expected']) * 100 : 0);
                        return array_merge($r, ['compliance' => round($score, 1)]);
                    })->sortByDesc(fn($r) => [$r['compliance'], $r['days_early']])->values();
                @endphp

                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;margin-bottom:18px;">
                    <div style="border:1px solid var(--gray-200);border-radius:var(--radius-md);padding:12px;background:#fff;">
                        <div style="font-size:.75rem;color:var(--gray-500);">Total Directorates</div>
                        <div style="font-size:1.2rem;font-weight:700;color:var(--gray-800);">{{ $totalDirectorates }}</div>
                    </div>
                    <div style="border:1px solid var(--gray-200);border-radius:var(--radius-md);padding:12px;background:#fff;">
                        <div style="font-size:.75rem;color:var(--gray-500);">Reports Submitted</div>
                        <div style="font-size:1.2rem;font-weight:700;color:#047857;">{{ $totalSubmitted }}</div>
                    </div>
                    <div style="border:1px solid var(--gray-200);border-radius:var(--radius-md);padding:12px;background:#fff;">
                        <div style="font-size:.75rem;color:var(--gray-500);">Pending Submissions</div>
                        <div style="font-size:1.2rem;font-weight:700;color:#b45309;">{{ $totalPending }}</div>
                    </div>
                    <div style="border:1px solid var(--gray-200);border-radius:var(--radius-md);padding:12px;background:#fff;">
                        <div style="font-size:.75rem;color:var(--gray-500);">Compliance Rate</div>
                        <div style="font-size:1.2rem;font-weight:700;color:#1d4ed8;">{{ $complianceRate }}%</div>
                    </div>
                    <div style="border:1px solid var(--gray-200);border-radius:var(--radius-md);padding:12px;background:#fff;">
                        <div style="font-size:.75rem;color:var(--gray-500);">Avg Timeliness</div>
                        <div style="font-size:1.2rem;font-weight:700;color:#7c3aed;">{{ $avgTimeliness }} day(s)</div>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1.2fr .8fr;gap:16px;">
                    <div style="border:1px solid var(--gray-200);border-radius:var(--radius-md);padding:14px;background:#fff;">
                        <div style="font-weight:600;font-size:.86rem;color:var(--gray-700);margin-bottom:10px;">
                            Comparative Compliance Analysis ({{ $currentMonth }})
                        </div>
                        <div id="dir-performance-chart" style="display:flex;flex-direction:column;gap:8px;">
                            @foreach($ranked as $row)
                                @php
                                    $percent = $row['compliance'];
                                    $barColor = $percent >= 100 ? '#059669' : ($percent >= 70 ? '#2563eb' : '#d97706');
                                @endphp
                                <div style="display:grid;grid-template-columns:170px 1fr 50px;gap:10px;align-items:center;">
                                    <span style="font-size:.76rem;color:var(--gray-600);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $row['name'] }}">
                                        {{ $row['name'] }}
                                    </span>
                                    <div style="height:10px;background:var(--gray-100);border-radius:999px;overflow:hidden;">
                                        <div style="height:100%;width:{{ $percent }}%;background:{{ $barColor }};"></div>
                                    </div>
                                    <span style="font-size:.74rem;color:var(--gray-700);font-weight:600;">{{ $percent }}%</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div style="border:1px solid var(--gray-200);border-radius:var(--radius-md);padding:14px;background:#fff;">
                        <div style="font-weight:600;font-size:.86rem;color:var(--gray-700);margin-bottom:8px;">Submission Summary</div>
                        <table style="width:100%;border-collapse:collapse;font-size:.78rem;">
                            <tbody>
                                <tr>
                                    <td style="padding:8px 4px;color:var(--gray-500);border-bottom:1px solid var(--gray-100);">Expected Reports</td>
                                    <td style="padding:8px 4px;text-align:right;font-weight:600;border-bottom:1px solid var(--gray-100);">{{ $totalExpected }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 4px;color:var(--gray-500);border-bottom:1px solid var(--gray-100);">Received Reports</td>
                                    <td style="padding:8px 4px;text-align:right;font-weight:600;border-bottom:1px solid var(--gray-100);">{{ $totalSubmitted }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 4px;color:var(--gray-500);border-bottom:1px solid var(--gray-100);">On-time Submissions</td>
                                    <td style="padding:8px 4px;text-align:right;font-weight:600;border-bottom:1px solid var(--gray-100);">{{ $onTimeCount }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 4px;color:var(--gray-500);">Late / Missing</td>
                                    <td style="padding:8px 4px;text-align:right;font-weight:600;">{{ $totalExpected - $onTimeCount }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div style="margin-top:10px;padding:10px;border-radius:10px;background:#f8fafc;color:#334155;font-size:.75rem;">
                            Directorate compliance currently stands at <strong>{{ $complianceRate }}%</strong>. Focus follow-up on pending directorates before month end.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Directorate Submission Metrics -->
        <div class="redas-card animate-fade-up delay-3" style="margin-top:24px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:var(--blue-50);color:var(--blue-600);">
                        <i class="fas fa-ranking-star"></i>
                    </div>
                    Directorate Compliance Ranking & Statistics
                </div>
            </div>
            <div class="card-body">
                <div style="overflow:auto;">
                    <table style="width:100%;border-collapse:collapse;min-width:760px;">
                        <thead>
                            <tr style="background:#f8fafc;">
                                <th style="text-align:left;padding:10px;font-size:.76rem;color:#475569;border-bottom:1px solid #e5e7eb;">Rank</th>
                                <th style="text-align:left;padding:10px;font-size:.76rem;color:#475569;border-bottom:1px solid #e5e7eb;">Directorate</th>
                                <th style="text-align:left;padding:10px;font-size:.76rem;color:#475569;border-bottom:1px solid #e5e7eb;">Submitted</th>
                                <th style="text-align:left;padding:10px;font-size:.76rem;color:#475569;border-bottom:1px solid #e5e7eb;">Expected</th>
                                <th style="text-align:left;padding:10px;font-size:.76rem;color:#475569;border-bottom:1px solid #e5e7eb;">Compliance</th>
                                <th style="text-align:left;padding:10px;font-size:.76rem;color:#475569;border-bottom:1px solid #e5e7eb;">Timeliness</th>
                                <th style="text-align:left;padding:10px;font-size:.76rem;color:#475569;border-bottom:1px solid #e5e7eb;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ranked as $idx => $row)
                                @php
                                    $status = $row['submitted'] >= $row['expected'] ? 'Compliant' : 'Pending';
                                    $statusBg = $status === 'Compliant' ? '#dcfce7' : '#fef3c7';
                                    $statusColor = $status === 'Compliant' ? '#166534' : '#92400e';
                                    $timelinessText = $row['submitted'] === 0
                                        ? 'No submission'
                                        : ($row['days_early'] >= 0 ? 'Early by '.$row['days_early'].' day(s)' : 'Late by '.abs($row['days_early']).' day(s)');
                                @endphp
                                <tr>
                                    <td style="padding:10px;font-size:.8rem;color:#0f172a;border-bottom:1px solid #f1f5f9;">{{ $idx + 1 }}</td>
                                    <td style="padding:10px;font-size:.8rem;color:#0f172a;border-bottom:1px solid #f1f5f9;">{{ $row['name'] }}</td>
                                    <td style="padding:10px;font-size:.8rem;color:#334155;border-bottom:1px solid #f1f5f9;">{{ $row['submitted'] }}</td>
                                    <td style="padding:10px;font-size:.8rem;color:#334155;border-bottom:1px solid #f1f5f9;">{{ $row['expected'] }}</td>
                                    <td style="padding:10px;font-size:.8rem;color:#334155;border-bottom:1px solid #f1f5f9;">{{ $row['compliance'] }}%</td>
                                    <td style="padding:10px;font-size:.8rem;color:#334155;border-bottom:1px solid #f1f5f9;">{{ $timelinessText }}</td>
                                    <td style="padding:10px;border-bottom:1px solid #f1f5f9;">
                                        <span style="display:inline-flex;align-items:center;padding:3px 8px;border-radius:999px;font-size:.72rem;font-weight:600;background:{{ $statusBg }};color:{{ $statusColor }};">
                                            {{ $status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

@include('partials.footer')
