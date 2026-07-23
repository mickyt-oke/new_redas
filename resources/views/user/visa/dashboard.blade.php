{{-- Visa & Residence Dashboard --}}

@include('partials.visa-header')

<main class="redas-content">

    <!-- Page header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="fas fa-passport"></i>
                Visa & Residence Annual Reporting Dashboard
            </h1>

            <p class="page-subtitle">
                Welcome back,
                <strong>{{ auth()->user()->name ?? 'Visa Officer' }}</strong>
                — {{ now()->format('l, d F Y') }}
            </p>
        </div>

        <a href="{{ route('visa.report') }}" class="btn-nis btn-primary-nis">
            <i class="fas fa-plus"></i>
            Create Annual Report
        </a>
    </div>

    <!-- Deadline alert -->
    <div
        class="animate-fade-up"
        style="background:linear-gradient(135deg,#fef3c7,#fde68a);
               border:1px solid #f59e0b;
               border-radius:var(--radius-md);
               padding:14px 18px;
               display:flex;
               align-items:center;
               gap:14px;
               margin-bottom:24px;">

        <div style="width:40px;height:40px;background:#f59e0b;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;color:#fff;">
            <i class="fas fa-calendar-exclamation"></i>
        </div>

        <div style="flex:1;">
            <strong style="color:#92400e;">
                Annual Reporting Exercise —
                {{ now()->endOfMonth()->format('d F Y') }}
            </strong>

            <p style="margin:0;color:#b45309;font-size:.82rem;">
               Complete and submit the Annual Report before the reporting deadline.
            </p>
        </div>

    </div>

    <!-- Statistics -->
    <div class="stats-grid animate-fade-up">

        <!-- 1. Staff Strength -->
        <div class="stat-card info">
            <div class="stat-header">
                <span class="stat-label">Staff Strength</span>
                <span class="stat-icon"><i class="fas fa-users"></i></span>
            </div>
            <div class="stat-value">{{ number_format($staffStrengthCount) }}</div>
            <div class="stat-change neutral">Latest Report</div>
        </div>

        <!-- 2. e-Migrant Centre -->
        <div class="stat-card gold">
            <div class="stat-header">
                <span class="stat-label">e-Migrant</span>
                <span class="stat-icon"><i class="fas fa-globe-africa"></i></span>
            </div>
            <div class="stat-value">{{ number_format($emigrantCount) }}</div>
            <div class="stat-change neutral">Latest Report</div>
        </div>

        <!-- 3. Quota Administration -->
        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-label">Quota Administration</span>
                <span class="stat-icon"><i class="fas fa-users-cog"></i></span>
            </div>
            <div class="stat-value">{{ number_format($quotaCount) }}</div>
            <div class="stat-change neutral">Latest Report</div>
        </div>

        <!-- 4. Residence Permits -->
        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-label">Residence Permits</span>
                <span class="stat-icon"><i class="fas fa-id-card"></i></span>
            </div>
            <div class="stat-value">{{ number_format($residencePermitsCount) }}</div>
            <div class="stat-change neutral">Latest Report</div>
        </div>

        <!-- 5. Free Trade Zone -->
        <div class="stat-card danger">
            <div class="stat-header">
                <span class="stat-label">Free Trade Zone</span>
                <span class="stat-icon"><i class="fas fa-industry"></i></span>
            </div>
            <div class="stat-value">{{ number_format($ftzEnterprisesCount) }}</div>
            <div class="stat-change neutral">Latest Report</div>
        </div>

        <!-- 6. CERPAC Production -->
        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-label">CERPAC Issued</span>
                <span class="stat-icon"><i class="fas fa-address-card"></i></span>
            </div>
            <div class="stat-value">{{ number_format($cerpacIssuedCount) }}</div>
            <div class="stat-change neutral">Latest Report</div>
        </div>

        <!-- 7. Visa Applications -->
        <div class="stat-card gold">
            <div class="stat-header">
                <span class="stat-label">Visa Applications</span>
                <span class="stat-icon"><i class="fas fa-passport"></i></span>
            </div>
            <div class="stat-value">{{ number_format($visaApplicationsCount) }}</div>
            <div class="stat-change neutral">Latest Report</div>
        </div>

        <!-- 8. TRV -->
        <div class="stat-card info">
            <div class="stat-header">
                <span class="stat-label">TRV Applications</span>
                <span class="stat-icon"><i class="fas fa-plane"></i></span>
            </div>
            <div class="stat-value">{{ number_format($trvCount) }}</div>
            <div class="stat-change neutral">Latest Report</div>
        </div>

        <!-- 9. PRV -->
        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-label">PRV Applications</span>
                <span class="stat-icon"><i class="fas fa-stamp"></i></span>
            </div>
            <div class="stat-value">{{ number_format($prvCount) }}</div>
            <div class="stat-change neutral">Latest Report</div>
        </div>

        <!-- 10. e-TWP -->
        <div class="stat-card gold">
            <div class="stat-header">
                <span class="stat-label">e-TWP Applications</span>
                <span class="stat-icon"><i class="fas fa-laptop"></i></span>
            </div>
            <div class="stat-value">{{ number_format($etwpCount) }}</div>
            <div class="stat-change neutral">Latest Report</div>
        </div>

        <!-- 11. Visa Summary -->
        <div class="stat-card info">
            <div class="stat-header">
                <span class="stat-label">Visa Applications Summary</span>
                <span class="stat-icon"><i class="fas fa-clipboard-list"></i></span>
            </div>
            <div class="stat-value">{{ number_format($visaSummaryCount) }}</div>
            <div class="stat-change neutral">Latest Report</div>
        </div>

        <!-- 12. ECOWAS -->
        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-label">ECOWAS Affairs</span>
                <span class="stat-icon"><i class="fas fa-flag"></i></span>
            </div>
            <div class="stat-value">{{ number_format($ecowasCount) }}</div>
            <div class="stat-change neutral">Latest Report</div>
        </div>

        <!-- 13. African Affairs -->
        <div class="stat-card gold">
            <div class="stat-header">
                <span class="stat-label">African Affairs</span>
                <span class="stat-icon"><i class="fas fa-earth-africa"></i></span>
            </div>
            <div class="stat-value">{{ number_format($africanCount) }}</div>
            <div class="stat-change neutral">Latest Report</div>
        </div>

    </div>

    <!-- ── Main Grid ── -->
    <div class="grid-2 animate-fade-up mt-4">

        <!-- Left Column: Analytics Chart -->
        <div class="redas-card">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:var(--nis-50);color:var(--nis-600);">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    Operational Performance Overview
                </div>
            </div>
            <div class="card-body" style="height: 280px; position: relative;">
                <canvas id="visaPerformanceChart"></canvas>
            </div>
        </div>

        <!-- Right Column: Recent Activities & Alerts -->
        <div style="display: flex; flex-direction: column; gap: 20px;">

            <!-- Recent Activities -->
            <div class="redas-card" style="margin: 0;">
                <div class="card-head">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:var(--purple-50);color:var(--purple-600);">
                            <i class="fas fa-history"></i>
                        </div>
                        Recent Activity Log
                    </div>
                    <a href="{{ route('visa.submissions') }}" class="btn-nis btn-ghost btn-sm">View All</a>
                </div>
                <div class="card-body no-pad">
                    @if($recentSubmissions->isEmpty())
                        <div style="padding: 24px; text-align: center; color: var(--gray-400); font-size: 0.8rem;">
                            No recent reports.
                        </div>
                    @else
                        @php
                        $statusConfig = [
                            'pending'  => ['badge-pending',  'Pending'],
                            'approved' => ['badge-approved', 'Approved'],
                            'queried'  => ['badge-review',   'Queried'],
                            'rejected' => ['badge-rejected', 'Rejected'],
                            'draft'    => ['badge-draft',    'Draft'],
                        ];
                        @endphp
                        @foreach($recentSubmissions as $sub)
                        @php [$badgeClass, $statusLabel] = $statusConfig[$sub->status] ?? ['badge-draft', $sub->status]; @endphp
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-bottom: 1px solid var(--gray-100);">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--nis-50); color: var(--nis-600); display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div>
                                    <div style="font-size: 0.82rem; font-weight: 600; color: var(--gray-800);">Year {{ $sub->period }} Report</div>
                                    <div style="font-size: 0.72rem; color: var(--gray-400);">Updated {{ $sub->updated_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            <span class="status-badge {{ $badgeClass }}" style="font-size: 0.68rem; padding: 2px 6px;">{{ $statusLabel }}</span>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Notifications & Alerts -->
            <div class="redas-card" style="margin: 0;">
                <div class="card-head">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:var(--gold-100);color:var(--gold-500);">
                            <i class="fas fa-bell"></i>
                        </div>
                        System Alerts & Notifications
                    </div>
                </div>
                <div class="card-body" style="display: flex; flex-direction: column; gap: 10px; font-size: 0.82rem;">
                    <!-- Queried return check -->
                    @php
                    $queried = $recentSubmissions->where('status', 'queried')->first();
                    @endphp
                    @if($queried)
                        <div style="display: flex; gap: 10px; background: #fff7ed; border: 1px solid #fed7aa; color: #c2410c; border-radius: var(--radius-md); padding: 10px 12px;">
                            <i class="fas fa-exclamation-triangle" style="margin-top: 2px;"></i>
                            <div>
                                <strong>Report Queried (Year {{ $queried->period }})</strong><br>
                                <span style="font-size: 0.76rem;">{{ $queried->comments ?? 'Please review and correct figures.' }}</span>
                            </div>
                        </div>
                    @endif

                    <div style="display: flex; gap: 10px; background: var(--nis-50); border: 1px solid var(--nis-100); color: var(--nis-800); border-radius: var(--radius-md); padding: 10px 12px;">
                        <i class="fas fa-info-circle" style="margin-top: 2px;"></i>
                        <div>
                            <strong>Reporting Deadline Active</strong><br>
                            <span style="font-size: 0.76rem;">Please ensure all annual reports are completed by the end of the month.</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('visaPerformanceChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [
                    'Staff Strength', 'e-Migrant', 'Quota Admin', 'Residence Permits', 
                    'Free Trade Zone', 'CERPAC Issued', 'Visa Applications', 'TRV Apps', 
                    'PRV Apps', 'e-TWP Apps', 'Visa Counter', 'ECOWAS', 'African Affairs'
                ],
                datasets: [{
                    label: 'Latest Report Metrics',
                    data: [
                        {{ $staffStrengthCount }},
                        {{ $emigrantCount }},
                        {{ $quotaCount }},
                        {{ $residencePermitsCount }},
                        {{ $ftzEnterprisesCount }},
                        {{ $cerpacIssuedCount }},
                        {{ $visaApplicationsCount }},
                        {{ $trvCount }},
                        {{ $prvCount }},
                        {{ $etwpCount }},
                        {{ $visaSummaryCount }},
                        {{ $ecowasCount }},
                        {{ $africanCount }}
                    ],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.75)',  // Blue (Staff)
                        'rgba(245, 158, 11, 0.75)',  // Gold (e-Migrant)
                        'rgba(11, 107, 58, 0.75)',   // Green (Quota)
                        'rgba(11, 107, 58, 0.75)',   // Green (Residence)
                        'rgba(239, 68, 68, 0.75)',   // Red (FTZ)
                        'rgba(11, 107, 58, 0.75)',   // Green (CERPAC)
                        'rgba(245, 158, 11, 0.75)',  // Gold (Visa)
                        'rgba(59, 130, 246, 0.75)',  // Blue (TRV)
                        'rgba(11, 107, 58, 0.75)',   // Green (PRV)
                        'rgba(245, 158, 11, 0.75)',  // Gold (e-TWP)
                        'rgba(59, 130, 246, 0.75)',  // Blue (Counter)
                        'rgba(11, 107, 58, 0.75)',   // Green (ECOWAS)
                        'rgba(245, 158, 11, 0.75)'   // Gold (African)
                    ],
                    borderColor: [
                        '#3b82f6', '#f59e0b', '#0B6B3A', '#0B6B3A', '#ef4444', 
                        '#0B6B3A', '#f59e0b', '#3b82f6', '#0B6B3A', '#f59e0b', 
                        '#3b82f6', '#0B6B3A', '#f59e0b'
                    ],
                    borderWidth: 1.5,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
});
</script>

@include('partials.footer')