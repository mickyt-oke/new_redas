@include('partials.header')

    <main class="redas-content">

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Generate Report</h1>
                <p class="page-subtitle">Generate PDF or Excel reports from your submitted returns.</p>
            </div>
        </div>

        @if(session('status'))
        <div style="background:#dcfce7;border:1px solid #86efac;border-radius:var(--radius-md);padding:12px 18px;display:flex;align-items:center;gap:12px;margin-bottom:20px;">
            <i class="fas fa-check-circle" style="color:#15803d;"></i>
            <span style="font-size:.88rem;color:#166534;font-weight:600;">{{ session('status') }}</span>
        </div>
        @endif

        <div class="grid-2" style="margin-bottom:24px;">

            <!-- Report Builder -->
            <div class="redas-card animate-fade-up">
                <div class="card-head">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:var(--nis-50);color:var(--nis-600);"><i class="fas fa-file-export"></i></div>
                        Report Builder
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ url('/user/reports/generate') }}" id="reportForm">
                        @csrf

                        <div class="fg" style="margin-bottom:14px;">
                            <label>Report Type</label>
                            <select name="report_type" class="ni ni-select" id="reportType" required onchange="updateSections()">
                                <option value="">Select Report Type</option>
                                <option value="submission_summary">Submission Summary</option>
                                <option value="monthly_return">Monthly Return</option>
                                <option value="quarterly_return">Quarterly Return</option>
                                <option value="annual_return">Annual Return</option>
                                <option value="compliance_report">Compliance Report</option>
                                <option value="full_export">Full Data Export</option>
                            </select>
                        </div>

                        <div class="form-grid-2" style="margin-bottom:14px;">
                            <div class="fg">
                                <label>Date From</label>
                                <input type="date" name="date_from" class="ni" value="{{ now()->startOfYear()->format('Y-m-d') }}" required>
                            </div>
                            <div class="fg">
                                <label>Date To</label>
                                <input type="date" name="date_to" class="ni" value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="fg" style="margin-bottom:14px;">
                            <label>Sections to Include</label>
                            <div id="sectionChecks" style="display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-top:6px;">
                                @foreach([
                                    'hrm'       => 'HRM',
                                    'border'    => 'Border Management',
                                    'finance'   => 'Finance & Accounts',
                                    'ic'        => 'Investigation & Compliance',
                                    'prs'       => 'PRS',
                                    'migration' => 'Migration',
                                    'ict'       => 'ICT',
                                    'works'     => 'Works & Logistics',
                                    'visa'      => 'Visa & Residency',
                                    'passport'  => 'Passport & Travel',
                                ] as $key => $label)
                                <label style="display:flex;align-items:center;gap:8px;font-size:.82rem;font-weight:500;cursor:pointer;padding:4px 0;">
                                    <input type="checkbox" name="sections[]" value="{{ $key }}" checked style="accent-color:var(--nis-600);">
                                    {{ $label }}
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="fg" style="margin-bottom:14px;">
                            <label>Output Format</label>
                            <div style="display:flex;gap:12px;margin-top:6px;">
                                <label style="display:flex;align-items:center;gap:8px;font-size:.84rem;cursor:pointer;">
                                    <input type="radio" name="format" value="pdf" checked style="accent-color:var(--nis-600);">
                                    <i class="fas fa-file-pdf" style="color:#dc2626;"></i> PDF
                                </label>
                                <label style="display:flex;align-items:center;gap:8px;font-size:.84rem;cursor:pointer;">
                                    <input type="radio" name="format" value="excel" style="accent-color:var(--nis-600);">
                                    <i class="fas fa-file-excel" style="color:#16a34a;"></i> Excel
                                </label>
                                <label style="display:flex;align-items:center;gap:8px;font-size:.84rem;cursor:pointer;">
                                    <input type="radio" name="format" value="csv" style="accent-color:var(--nis-600);">
                                    <i class="fas fa-file-csv" style="color:#2563eb;"></i> CSV
                                </label>
                            </div>
                        </div>

                        <div style="display:flex;gap:10px;margin-top:4px;">
                            <button type="button" class="btn-nis btn-ghost btn-sm" onclick="toggleAllSections(false)">
                                <i class="fas fa-times-circle"></i> Deselect All
                            </button>
                            <button type="button" class="btn-nis btn-ghost btn-sm" onclick="toggleAllSections(true)">
                                <i class="fas fa-check-circle"></i> Select All
                            </button>
                            <button type="submit" class="btn-nis btn-primary-nis btn-sm" style="margin-left:auto;">
                                <i class="fas fa-download"></i> Generate Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Recent Reports -->
            <div style="display:flex;flex-direction:column;gap:20px;">

                <!-- Quick Generate -->
                <div class="redas-card animate-fade-up delay-1">
                    <div class="card-head">
                        <div class="card-head-title">
                            <div class="card-head-icon" style="background:var(--gold-100);color:var(--gold-500);"><i class="fas fa-bolt"></i></div>
                            Quick Generate
                        </div>
                    </div>
                    <div class="card-body" style="display:flex;flex-direction:column;gap:10px;">
                        <button class="btn-nis btn-outline-nis full-width" onclick="quickGenerate('monthly_return')">
                            <i class="fas fa-file-alt"></i> This Month's Return — PDF
                        </button>
                        <button class="btn-nis btn-outline-nis full-width" onclick="quickGenerate('quarterly_return')">
                            <i class="fas fa-file-alt"></i> This Quarter's Return — PDF
                        </button>
                        <button class="btn-nis btn-ghost full-width" onclick="quickGenerate('full_export')">
                            <i class="fas fa-file-excel" style="color:#16a34a;"></i> Full Year Export — Excel
                        </button>
                        <button class="btn-nis btn-ghost full-width" onclick="quickGenerate('submission_summary')">
                            <i class="fas fa-chart-bar"></i> Submission Summary — PDF
                        </button>
                    </div>
                </div>

                <!-- Recent Generated -->
                <div class="redas-card animate-fade-up delay-2">
                    <div class="card-head">
                        <div class="card-head-title">
                            <div class="card-head-icon" style="background:var(--nis-50);color:var(--nis-600);"><i class="fas fa-history"></i></div>
                            Recently Generated
                        </div>
                    </div>
                    <div class="card-body no-pad">
                        @foreach([
                            ['April 2025 Monthly Return',  'PDF',   '27 Apr 2025', 'fas fa-file-pdf',   '#dc2626'],
                            ['Q1 2025 Quarterly Return',   'PDF',   '05 Apr 2025', 'fas fa-file-pdf',   '#dc2626'],
                            ['Full Data Export — 2025',    'Excel', '01 Apr 2025', 'fas fa-file-excel', '#16a34a'],
                            ['Submission Summary YTD',     'PDF',   '31 Mar 2025', 'fas fa-file-pdf',   '#dc2626'],
                        ] as [$name, $fmt, $date, $icon, $color])
                        <div style="display:flex;align-items:center;gap:12px;padding:10px 16px;border-bottom:1px solid var(--gray-100);">
                            <i class="{{ $icon }}" style="color:{{ $color }};font-size:1.2rem;flex-shrink:0;"></i>
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:.82rem;font-weight:600;color:var(--gray-800);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $name }}</div>
                                <div style="font-size:.72rem;color:var(--gray-400);">{{ $fmt }} &bull; {{ $date }}</div>
                            </div>
                            <button class="btn-nis btn-ghost btn-sm" title="Download" onclick="REDAS.showToast('Downloading…','success')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </main>

<script>
function toggleAllSections(state) {
    document.querySelectorAll('#sectionChecks input[type=checkbox]').forEach(cb => cb.checked = state);
}

function quickGenerate(type) {
    document.getElementById('reportType').value = type;
    REDAS.showToast('Generating report…', 'info');
    setTimeout(() => REDAS.showToast('Report ready for download.', 'success'), 1800);
}
</script>

@include('partials.footer')
