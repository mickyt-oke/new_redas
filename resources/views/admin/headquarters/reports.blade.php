@extends('admin.headquarters.layout')

@section('title', 'Report Generation')

@section('content')

<main class="redas-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">Report Generation</h1>
            <p class="page-subtitle">Generate consolidated Excel reports from pre-defined templates. Every return from all users, directorates, state commands and CGIS units in the selected period is merged into a single worksheet.</p>
        </div>
    </div>

    @if(session('status'))
        <div style="margin-bottom:16px;padding:12px 14px;border-radius:10px;border:1px solid #bbf7d0;background:#f0fdf4;color:#166534;">
            {{ session('status') }}
        </div>
    @endif

    @if(session('error'))
        <div style="margin-bottom:16px;padding:12px 14px;border-radius:10px;border:1px solid #fecaca;background:#fef2f2;color:#b91c1c;">
            <i class="fas fa-exclamation-circle" style="margin-right:6px;"></i>{{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div style="margin-bottom:16px;padding:12px 14px;border-radius:10px;border:1px solid #fecaca;background:#fef2f2;color:#b91c1c;">
            <ul style="margin:0;padding-left:18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:20px;align-items:start;">
        <div class="redas-card">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#dcfce7;color:#15803d;">
                        <i class="fas fa-file-excel"></i>
                    </div>
                    Generate Consolidated Report
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.hq.reports.generate') }}">
                    @csrf
                    <div style="display:flex;flex-direction:column;gap:14px;">
                        <div>
                            <label for="report_type" style="display:block;font-size:.78rem;font-weight:600;color:var(--gray-600);margin-bottom:4px;">Report Template</label>
                            <select id="report_type" name="report_type" required style="width:100%;padding:9px 12px;border:1px solid var(--gray-200);border-radius:8px;font-size:.88rem;">
                                <option value="quarterly" @selected(old('report_type') === 'quarterly')>Quarterly Report</option>
                                <option value="biannual" @selected(old('report_type') === 'biannual')>Bi-Annual Report</option>
                                <option value="annual" @selected(old('report_type', 'annual') === 'annual')>Annual Report</option>
                            </select>
                        </div>
                        <div>
                            <label for="year" style="display:block;font-size:.78rem;font-weight:600;color:var(--gray-600);margin-bottom:4px;">Year</label>
                            <select id="year" name="year" required style="width:100%;padding:9px 12px;border:1px solid var(--gray-200);border-radius:8px;font-size:.88rem;">
                                @for($y = now()->year; $y >= now()->year - 5; $y--)
                                    <option value="{{ $y }}" @selected((int) old('year', now()->year) === $y)>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div id="partQuarter">
                            <label for="part_quarter" style="display:block;font-size:.78rem;font-weight:600;color:var(--gray-600);margin-bottom:4px;">Quarter</label>
                            <select id="part_quarter" name="part" style="width:100%;padding:9px 12px;border:1px solid var(--gray-200);border-radius:8px;font-size:.88rem;">
                                <option value="1">Q1 (Jan – Mar)</option>
                                <option value="2">Q2 (Apr – Jun)</option>
                                <option value="3">Q3 (Jul – Sep)</option>
                                <option value="4">Q4 (Oct – Dec)</option>
                            </select>
                        </div>
                        <div id="partHalf" style="display:none;">
                            <label for="part_half" style="display:block;font-size:.78rem;font-weight:600;color:var(--gray-600);margin-bottom:4px;">Half</label>
                            <select id="part_half" style="width:100%;padding:9px 12px;border:1px solid var(--gray-200);border-radius:8px;font-size:.88rem;" disabled>
                                <option value="1">First Half (Jan – Jun)</option>
                                <option value="2">Second Half (Jul – Dec)</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-nis" style="justify-content:center;">
                            <i class="fas fa-file-arrow-down"></i> Generate &amp; Download Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="redas-card">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#e0f2fe;color:#0369a1;">
                        <i class="fas fa-circle-info"></i>
                    </div>
                    Available Templates
                </div>
            </div>
            <div class="card-body">
                <div style="display:flex;flex-direction:column;gap:12px;">
                    @foreach($templates as $type => $template)
                        <div style="border:1px solid var(--gray-200);border-radius:10px;padding:12px;">
                            <div style="font-weight:700;color:var(--gray-900);font-size:.9rem;">{{ $template['title'] }}</div>
                            <div style="font-size:.78rem;color:var(--gray-500);margin-top:2px;">
                                Periods:
                                @foreach($template['parts'] as $part)
                                    <span class="status-badge badge-draft" style="margin-right:4px;">{{ $part['label'] }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                <div style="margin-top:14px;padding:12px;border:1px solid var(--gray-200);border-radius:10px;background:var(--gray-50);font-size:.8rem;color:var(--gray-600);">
                    <strong>Each report consolidates into one Excel sheet:</strong> Ref, Formation/Directorate, CGIS Unit, Category, Report Period, Reporting Officer, Service Number, Status, Current Stage, Submitted At, Last Action At and Comment — for every return submitted within the selected period, across all users and formations.
                </div>
            </div>
        </div>
    </div>
</main>

@push('scripts')
<script>
    const typeSelect = document.getElementById('report_type');
    const quarterWrap = document.getElementById('partQuarter');
    const halfWrap = document.getElementById('partHalf');
    const quarterSelect = document.getElementById('part_quarter');
    const halfSelect = document.getElementById('part_half');

    function syncPartFields() {
        const type = typeSelect.value;
        quarterWrap.style.display = type === 'quarterly' ? '' : 'none';
        halfWrap.style.display = type === 'biannual' ? '' : 'none';
        quarterSelect.disabled = type !== 'quarterly';
        halfSelect.disabled = type !== 'biannual';
        if (type === 'biannual') {
            halfSelect.name = 'part';
            quarterSelect.removeAttribute('name');
        } else if (type === 'quarterly') {
            quarterSelect.name = 'part';
            halfSelect.removeAttribute('name');
        } else {
            quarterSelect.removeAttribute('name');
            halfSelect.removeAttribute('name');
        }
    }

    typeSelect.addEventListener('change', syncPartFields);
    syncPartFields();
</script>
@endpush

@endsection
