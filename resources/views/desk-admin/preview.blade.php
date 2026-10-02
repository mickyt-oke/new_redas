@extends('desk-admin.layout')

@section('content')
@php
    $data = is_array($application->return_data) ? $application->return_data : [];
    $skipKeys = ['directorate_slug', 'cgis_unit_slug', 'data_consent', 'report_period', 'reporting_officer', 'command_name', 'period', 'return_type', 'supporting_documents', 'attachments'];

    $directorateNames = [];
    foreach (\App\Http\Controllers\Web\DashboardController::directorates() as $s => $meta) {
        $directorateNames[$s] = $meta['name'];
    }

    $status = strtolower((string) $application->status);
    $statusStyle = match (true) {
        $status === 'approved' => 'background:#ecfdf5;color:#166534;border:1px solid #86efac;',
        in_array($status, ['rejected', 'queried', 'returned'], true) => 'background:#fef2f2;color:#991b1b;border:1px solid #fca5a5;',
        default => 'background:#fef9c3;color:#854d0e;border:1px solid #fde047;',
    };
@endphp

@php
$sections = \App\Services\PreviewRenderer::buildSections($data, [
    'skipKeys' => $skipKeys,
    'directorateNames' => $directorateNames,
]);
@endphp

<style>
    .preview-section-title { font-size:.95rem; font-weight:700; color:var(--nis-700); margin:20px 0 8px; border-bottom:1px solid var(--gray-200); padding-bottom:4px; }
    .preview-sub-section-title { font-size:.85rem; font-weight:700; color:var(--gray-700); margin:12px 0 6px; }
    table.preview-report-table { width:100%; border-collapse:collapse; font-size:.82rem; margin-bottom:8px; }
    table.preview-report-table th, table.preview-report-table td { border:1px solid var(--gray-200); padding:6px 10px; text-align:left; vertical-align:top; }
    table.preview-report-table th { background:var(--nis-50); font-weight:700; color:var(--nis-800); }
    table.preview-report-table td.kv-key { width:38%; font-weight:600; background:#f9fafb; }
    .preview-section-card { border:1px solid var(--gray-200); border-radius:10px; padding:16px 18px 8px; margin:0 0 16px; background:#fff; }
    .preview-section-card .section-title { display:flex; align-items:center; gap:8px; font-size:.95rem; font-weight:700; color:var(--nis-700); margin:0 0 10px; border-bottom:2px solid var(--nis-700); padding-bottom:6px; }
    .preview-section-card .section-num { display:inline-flex; align-items:center; justify-content:center; min-width:22px; height:22px; padding:0 6px; border-radius:6px; background:var(--nis-700); color:#fff; font-size:.72rem; font-weight:700; }
</style>

<main class="redas-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">Submission #{{ $application->id }}</h1>
            <p class="page-subtitle">Review the return below before approval or return.</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('user.submissions.pdf', $application) }}" class="btn-nis btn-outline-nis">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>
            <a href="{{ route('desk.admin.submissions.download', $application) }}" class="btn-nis btn-outline-nis">
                <i class="fas fa-download"></i> Download CSV
            </a>
            <a href="{{ route('user.desk.home') }}" class="btn-nis btn-ghost">
                <i class="fas fa-arrow-left"></i> Back to Review Dashboard
            </a>
        </div>
    </div>

    @if(session('status'))
        <div style="margin-bottom:16px;padding:12px 14px;border-radius:10px;border:1px solid #bbf7d0;background:#f0fdf4;color:#166534;">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div style="margin-bottom:16px;padding:12px 14px;border-radius:10px;border:1px solid #fca5a5;background:#fef2f2;color:#991b1b;">
            @foreach($errors->all() as $error)
                <div><i class="fas fa-exclamation-circle" style="margin-right:6px;"></i>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- Report header --}}
    <div class="redas-card" style="margin-bottom:20px;">
        <div class="card-body" style="border-bottom:3px solid var(--nis-700);">
            <h2 style="font-size:1.15rem;margin:0 0 4px;color:var(--nis-700);">Nigeria Immigration Service</h2>
            <p style="margin:2px 0;font-size:.85rem;color:var(--gray-600);">
                {{ $directorateNames[$data['directorate_slug'] ?? $data['cgis_unit_slug'] ?? null] ?? ucwords(str_replace(['_', '-'], ' ', (string) ($data['directorate_slug'] ?? $application->scope_code ?? 'State'))) }} — Monthly Return
            </p>
            <div style="display:flex;flex-wrap:wrap;gap:8px 28px;margin-top:10px;font-size:.85rem;">
                <span><strong>Report Period:</strong> {{ $data['report_period'] ?? $data['period'] ?? '—' }}</span>
                <span><strong>Reporting Officer:</strong> {{ $data['reporting_officer'] ?? optional($application->user)->name ?? '—' }}</span>
                <span><strong>Submitted:</strong> {{ optional($application->created_at)->format('d M Y, H:i') ?? '—' }}</span>
                <span><strong>Status:</strong>
                    <span style="display:inline-block;padding:3px 10px;border-radius:999px;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;{{ $statusStyle }}">
                        {{ ucfirst($application->status ?? 'pending') }}
                    </span>
                </span>
            </div>
        </div>
    </div>

    {{-- Sectional return data with pagination --}}
    <div data-preview-paginator>
        @include('partials.preview-pagination')

        <div class="preview-paginator">
            <div class="preview-paginator-top">
                <div class="preview-paginator-title preview-counter">Section 1 of {{ count($sections) }}</div>
                <div class="preview-paginator-controls">
                    <button type="button" class="preview-prev-btn" disabled><i class="fas fa-arrow-left"></i> Previous</button>
                    <button type="button" class="preview-next-btn">Next <i class="fas fa-arrow-right"></i></button>
                    <button type="button" class="preview-all-btn">Show all</button>
                </div>
            </div>
            <div class="preview-section-tabs"></div>
        </div>

        @forelse($sections as $index => $section)
        <div class="redas-card preview-section {{ $loop->first ? 'active' : '' }}" style="margin-bottom:16px;" data-label="{{ $section['label'] }}">
            <div class="card-body">
                <h2 class="section-title"><span class="section-num">{{ $index + 1 }}</span> {{ $section['label'] }}</h2>
                @if($section['html'])
                    {!! $section['html'] !!}
                @else
                    <p style="padding:12px;color:#64748b;">No data entered for this section.</p>
                @endif
            </div>
        </div>
        @empty
        <div class="redas-card preview-section active" style="margin-bottom:16px;" data-label="No Data">
            <div class="card-body">
                <p style="padding:12px;color:#64748b;">No return data is available to preview.</p>
            </div>
        </div>
        @endforelse
    </div>

    @include('partials.document-viewer', ['docRoute' => 'desk.admin.submissions.document'])

    {{-- Review comment history --}}
    @php
        $commentHistory = $application->relationLoaded('reviewComments') ? $application->reviewComments : collect();
    @endphp
    @if($commentHistory->isNotEmpty())
        <div class="redas-card" style="padding:16px; margin-top:20px;">
            <h3 style="margin:0 0 12px;font-size:.95rem;font-weight:700;color:var(--gray-800);">Review History</h3>
            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach($commentHistory as $entry)
                    @php
                        [$badgeBg, $badgeColor, $badgeIcon] = match ($entry->action) {
                            'approved' => ['#ecfdf5', '#15803d', 'fa-check'],
                            'rejected' => ['#fef2f2', '#b91c1c', 'fa-undo'],
                            'resubmitted' => ['#eff6ff', '#1d4ed8', 'fa-paper-plane'],
                            'submitted' => ['#eff6ff', '#1d4ed8', 'fa-inbox'],
                            default => ['#f8fafc', '#475569', 'fa-comment'],
                        };
                    @endphp
                    <div style="display:flex;gap:10px;align-items:flex-start;">
                        <span style="flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:999px;background:{{ $badgeBg }};color:{{ $badgeColor }};font-size:.7rem;"><i class="fas {{ $badgeIcon }}"></i></span>
                        <div style="flex:1;">
                            <div style="font-size:.8rem;color:var(--gray-800);">
                                <strong>{{ optional($entry->user)->name ?? 'System' }}</strong>
                                <span style="color:var(--gray-500);">· {{ ucfirst($entry->action) }} at {{ str_replace('_', ' ', $entry->stage) }} · {{ optional($entry->created_at)->format('d M Y, H:i') }}</span>
                            </div>
                            @if($entry->comment)
                                <div style="font-size:.84rem;color:var(--gray-700);margin-top:2px;">{{ $entry->comment }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Review actions --}}
    <div class="redas-card" style="padding:16px; margin-top:20px;">
        @if($canReview ?? false)
            <div style="display:flex;justify-content:flex-end;gap:12px;flex-wrap:wrap;align-items:center;">
                <form method="POST" action="{{ route($approveRoute, $application) }}" style="display:flex;gap:8px;flex-wrap:wrap;margin:0;">
                    @csrf
                    @method('PATCH')
                    <input type="text" name="note" maxlength="1000" placeholder="Approval note (optional)" style="padding:8px 10px;border:1px solid #cbd5e1;border-radius:8px;font-size:.82rem;min-width:200px;">
                    <button type="submit" class="btn-nis btn-primary-nis"><i class="fas fa-check"></i> Approve</button>
                </form>
                <form method="POST" action="{{ route($rejectRoute, $application) }}" style="display:flex;gap:8px;flex-wrap:wrap;margin:0;">
                    @csrf
                    @method('PATCH')
                    <input type="text" name="comment" maxlength="1000" required placeholder="Rejection reason (required)" style="padding:8px 10px;border:1px solid #cbd5e1;border-radius:8px;font-size:.82rem;min-width:200px;">
                    <button type="submit" class="btn-nis" style="background:#b91c1c;color:#fff;"><i class="fas fa-undo"></i> Return for Correction</button>
                </form>
            </div>
        @else
            <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:center;">
                <span style="font-size:.84rem;color:var(--gray-500);">This submission is not awaiting your action.</span>
                <a href="{{ auth()->user()?->user_category === 'hq_admin' ? route('admin.submissions') : route('user.desk.home') }}" class="btn-nis btn-ghost">Back to dashboard</a>
            </div>
        @endif
    </div>
</main>
@endsection
