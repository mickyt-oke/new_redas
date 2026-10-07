@php
    $application = $application ?? null;
    $data = is_array($application?->return_data) ? $application->return_data : [];
    $skipKeys = ['directorate_slug', 'cgis_unit_slug', 'data_consent', 'report_period', 'reporting_officer', 'command_name', 'period', 'return_type', 'supporting_documents', 'attachments'];

    $directorateNames = [];
    foreach (\App\Http\Controllers\Web\DashboardController::directorates() as $s => $meta) {
        $directorateNames[$s] = $meta['name'];
    }

    $sections = \App\Services\PreviewRenderer::buildSections($data, [
        'skipKeys' => $skipKeys,
        'directorateSlug' => $data['directorate_slug'] ?? $data['cgis_unit_slug'] ?? null,
        'directorateNames' => $directorateNames,
        'sectionLabels' => [
            'personnel' => 'Personnel Strength',
            'operations' => 'Operations Summary',
            'logistics' => 'Logistics / Equipment',
            'finance' => 'Finance / Budget Summary',
            'challenges' => 'Challenges & Way Forward',
        ],
    ]);

    $status = strtolower((string) ($application?->status ?? 'pending'));
    $statusClass = match (true) {
        $status === 'approved' => 'badge-approved',
        in_array($status, ['rejected', 'queried', 'returned'], true) => 'badge-rejected',
        default => 'badge-pending',
    };

    $documentRoute = $documentRoute ?? 'desk.admin.submissions.document';
    $backRoute = $backRoute ?? 'user.desk.home';
    $backLabel = $backLabel ?? 'Back to Dashboard';
    $canReview = $canReview ?? false;
    $approveRoute = $approveRoute ?? 'desk.admin.submissions.approve';
    $rejectRoute = $rejectRoute ?? 'desk.admin.submissions.reject';
    $pdfRoute = $pdfRoute ?? 'user.submissions.pdf';
    $downloadRoute = $downloadRoute ?? 'desk.admin.submissions.download';

    $commentHistory = $application?->relationLoaded('reviewComments') ? $application->reviewComments : collect();
    $timeline = $timeline ?? collect();

    $formatPreviewHtml = function (string $html): string {
        if (trim($html) === '') {
            return '';
        }
        // Wrap every report table in a responsive Bootstrap container and add
        // Bootstrap table classes while preserving the existing report-table class.
        $wrapped = preg_replace('/<table\b([^>]*)class="(.*?)report-table(.*?)"([^>]*)>/i',
            '<div class="table-responsive"><table$1class="$2report-table table table-nis table-hover table-bordered$3"$4>',
            $html);
        return str_replace('</table>', '</table></div>', $wrapped ?? $html);
    };
@endphp

<style>
    .preview-section-card { border:1px solid var(--gray-200); border-radius:10px; padding:16px 18px 8px; margin:0 0 16px; background:#fff; }
    .preview-section-card .section-title { display:flex; align-items:center; gap:8px; font-size:.95rem; font-weight:700; color:var(--nis-700); margin:0 0 10px; border-bottom:2px solid var(--nis-700); padding-bottom:6px; }
    .preview-section-card .section-num { display:inline-flex; align-items:center; justify-content:center; min-width:22px; height:22px; padding:0 6px; border-radius:6px; background:var(--nis-700); color:#fff; font-size:.72rem; font-weight:700; }
    .preview-section-card .sub-section-title { font-size:.85rem; font-weight:700; color:var(--gray-700); margin:12px 0 6px; }
    @media print {
        .nav-tabs-nis { display:none !important; }
        .tab-pane { display:block !important; opacity:1 !important; }
    }
</style>

<div class="page-header" style="margin-bottom:18px;">
    <div>
        <h1 class="page-title">Submission #{{ $application?->id }}</h1>
        <p class="page-subtitle">Review the return below before approval or query.</p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a href="{{ URL::signedRoute($pdfRoute, ['applicationHash' => \App\Services\HashidService::encode($application?->id)]) }}" class="btn-nis btn-outline-nis" target="_blank">
            <i class="fas fa-file-pdf"></i> Download PDF
        </a>
        <a href="{{ URL::signedRoute($downloadRoute, ['applicationHash' => \App\Services\HashidService::encode($application?->id)]) }}" class="btn-nis btn-outline-nis">
            <i class="fas fa-download"></i> Download CSV
        </a>
        <a href="{{ route($backRoute) }}" class="btn-nis btn-ghost">
            <i class="fas fa-arrow-left"></i> {{ $backLabel }}
        </a>
    </div>
</div>

@if(session('status'))
    <div class="alert alert-success" style="margin-bottom:16px;">
        <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger" style="margin-bottom:16px;">
        @foreach($errors->all() as $error)
            <div><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="redas-card" style="margin-bottom:20px;">
    <div class="card-body" style="border-bottom:3px solid var(--nis-700);">
        <h2 style="font-size:1.15rem;margin:0 0 4px;color:var(--nis-700);">Nigeria Immigration Service</h2>
        <p style="margin:2px 0;font-size:.85rem;color:var(--gray-600);">
            {{ $directorateNames[$data['directorate_slug'] ?? $data['cgis_unit_slug'] ?? null] ?? ucwords(str_replace(['_', '-'], ' ', (string) ($data['directorate_slug'] ?? $application?->scope_code ?? 'State'))) }} — Monthly Return
        </p>
        <div style="display:flex;flex-wrap:wrap;gap:8px 28px;margin-top:10px;font-size:.85rem;">
            <span><strong>Report Period:</strong> {{ $data['report_period'] ?? $data['period'] ?? '—' }}</span>
            <span><strong>Reporting Officer:</strong> {{ $data['reporting_officer'] ?? optional($application?->user)->name ?? '—' }}</span>
            <span><strong>Submitted:</strong> {{ optional($application?->created_at)->format('d M Y, H:i') ?? '—' }}</span>
            <span><strong>Status:</strong> <span class="status-badge {{ $statusClass }}">{{ ucfirst($application?->status ?? 'pending') }}</span></span>
        </div>
    </div>
</div>

@if(count($sections) > 0)
    <ul class="nav nav-tabs nav-tabs-nis" id="previewSectionTabs" role="tablist">
        @foreach($sections as $index => $section)
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                        id="preview-tab-{{ $index }}"
                        data-bs-toggle="tab"
                        data-bs-target="#preview-pane-{{ $index }}"
                        type="button"
                        role="tab"
                        aria-controls="preview-pane-{{ $index }}"
                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                    <span class="badge bg-secondary me-1" style="font-size:.65rem;">{{ $index + 1 }}</span>
                    {{ $section['label'] }}
                </button>
            </li>
        @endforeach
    </ul>

    <div class="tab-content tab-content-nis" id="previewSectionContent">
        @foreach($sections as $index => $section)
            @php $sectionHtml = $formatPreviewHtml($section['html'] ?? ''); @endphp
            @if($sectionHtml !== '')
                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                     id="preview-pane-{{ $index }}"
                     role="tabpanel"
                     aria-labelledby="preview-tab-{{ $index }}">
                    <div class="redas-card preview-section-card" style="margin-bottom:16px;">
                        <div class="card-body">
                            <h2 class="section-title"><span class="section-num">{{ $index + 1 }}</span> {{ $section['label'] }}</h2>
                            {!! $sectionHtml !!}
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@else
    <div class="redas-card" style="margin-bottom:16px;">
        <div class="card-body">
            <div class="empty-state">
                <i class="fas fa-clipboard-list"></i>
                <p>No return data is available to preview.</p>
            </div>
        </div>
    </div>
@endif

@include('partials.document-viewer', ['docRoute' => $documentRoute])

@if($timeline->isNotEmpty())
    <div class="redas-card" style="padding:16px; margin-top:20px;">
        <h3 style="margin:0 0 12px;font-size:.95rem;font-weight:700;color:var(--gray-800);">Process Timeline</h3>
        <div style="display:flex;flex-direction:column;gap:8px;">
            @foreach($timeline as $entry)
                <div style="display:flex;gap:10px;align-items:flex-start;">
                    <span style="flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:999px;background:var(--nis-50);color:var(--nis-700);font-size:.7rem;"><i class="fas fa-circle-check"></i></span>
                    <div style="flex:1;">
                        <div style="font-size:.8rem;color:var(--gray-800);">
                            <strong>{{ $entry['by'] ?? 'System' }}</strong>
                            <span style="color:var(--gray-500);">· {{ ucfirst($entry['action'] ?? 'submitted') }} at {{ $entry['stage'] ?? '—' }} · {{ isset($entry['at']) ? \Carbon\Carbon::parse($entry['at'])->format('d M Y, H:i') : '—' }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

@if($commentHistory->isNotEmpty())
    <div class="redas-card" style="padding:16px; margin-top:20px;">
        <h3 style="margin:0 0 12px;font-size:.95rem;font-weight:700;color:var(--gray-800);">Review History</h3>
        <div style="display:flex;flex-direction:column;gap:10px;">
            @foreach($commentHistory as $entry)
                @php
                    [$badgeClass, $badgeIcon] = match ($entry->action) {
                        'approved' => ['badge-approved', 'fa-check'],
                        'rejected' => ['badge-rejected', 'fa-undo'],
                        'resubmitted' => ['badge-pending', 'fa-paper-plane'],
                        'submitted' => ['badge-pending', 'fa-inbox'],
                        default => ['badge-draft', 'fa-comment'],
                    };
                @endphp
                <div style="display:flex;gap:10px;align-items:flex-start;">
                    <span style="flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:999px;background:var(--nis-50);color:var(--nis-700);font-size:.7rem;"><i class="fas {{ $badgeIcon }}"></i></span>
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

<div class="redas-card" style="padding:16px; margin-top:20px;">
    @if($canReview)
        <div style="display:flex;justify-content:flex-end;gap:12px;flex-wrap:wrap;align-items:center;">
            <form method="POST" action="{{ URL::signedRoute($approveRoute, ['applicationHash' => \App\Services\HashidService::encode($application?->id)]) }}" style="display:flex;gap:8px;flex-wrap:wrap;margin:0;" class="needs-validation" novalidate>
                @csrf
                @method('PATCH')
                <input type="text" name="note" maxlength="1000" placeholder="Approval note (optional)" style="padding:8px 10px;border:1px solid #cbd5e1;border-radius:8px;font-size:.82rem;min-width:200px;">
                <button type="submit" class="btn-nis btn-primary-nis"><i class="fas fa-check"></i> Approve</button>
            </form>
            <form method="POST" action="{{ URL::signedRoute($rejectRoute, ['applicationHash' => \App\Services\HashidService::encode($application?->id)]) }}" style="display:flex;gap:8px;flex-wrap:wrap;margin:0;" class="needs-validation" novalidate>
                @csrf
                @method('PATCH')
                <input type="text" name="comment" maxlength="1000" required placeholder="Rejection reason (required)" style="padding:8px 10px;border:1px solid #cbd5e1;border-radius:8px;font-size:.82rem;min-width:200px;">
                <button type="submit" class="btn-nis" style="background:#b91c1c;color:#fff;"><i class="fas fa-undo"></i> Return for Correction</button>
            </form>
        </div>
    @else
        <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:center;">
            <span style="font-size:.84rem;color:var(--gray-500);">This submission is not awaiting your action.</span>
            <a href="{{ route($backRoute) }}" class="btn-nis btn-ghost">Back to dashboard</a>
        </div>
    @endif
</div>
