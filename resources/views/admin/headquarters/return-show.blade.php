@extends($layout ?? 'admin.headquarters.layout')

@section('title', 'Return Detail')

@section('content')

@php
    $data = $application->return_data ?? [];
    $skipKeys = ['directorate_slug', 'cgis_unit_slug', 'data_consent', 'report_period', 'reporting_officer', 'command_name', 'period', 'return_type', 'supporting_documents', 'attachments'];
    $badge = match ($application->status) {
        'approved' => 'badge-approved',
        'returned', 'rejected' => 'badge-rejected',
        default => 'badge-pending',
    };
    $backRoute = $backRoute ?? 'admin.hq.returns';
    $documentRoute = $documentRoute ?? 'admin.hq.returns.document';
    $commentHistory = $application->relationLoaded('reviewComments') ? $application->reviewComments : collect();

    $directorateNames = [];
    foreach (\App\Http\Controllers\Web\DashboardController::directorates() as $s => $meta) {
        $directorateNames[$s] = $meta['name'];
    }
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
</style>

<main class="redas-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">Return RET-{{ str_pad($application->id, 5, '0', STR_PAD_LEFT) }}</h1>
            <p class="page-subtitle">{{ $data['command_name'] ?? $application->scope_code ?? '—' }} — submitted {{ optional($application->created_at)->format('d M Y, H:i') }}. <strong>View-only</strong> record.</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
            <span class="status-badge {{ $badge }}">{{ ucfirst($application->status) }}</span>
            <a href="{{ route('user.submissions.pdf', $application) }}" class="btn-nis btn-outline-nis" target="_blank">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <a href="{{ route($backRoute) }}" class="btn-nis btn-ghost">
                <i class="fas fa-arrow-left"></i> Back to Returns
            </a>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:minmax(0,2fr) minmax(0,1fr);gap:20px;align-items:start;">
        <div data-preview-paginator>
            @include('partials.preview-pagination')

            <div class="redas-card" style="margin-bottom:20px;">
                <div class="card-head">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:#e0f2fe;color:#0369a1;">
                            <i class="fas fa-circle-info"></i>
                        </div>
                        Return Summary
                    </div>
                </div>
                <div class="card-body">
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;">
                        <div style="border:1px solid var(--gray-200);border-radius:10px;padding:10px;">
                            <div style="font-size:.74rem;color:var(--gray-500);">Reporting Officer</div>
                            <div style="font-weight:700;color:var(--gray-900);">{{ $data['reporting_officer'] ?? $application->user->name ?? '—' }}</div>
                        </div>
                        <div style="border:1px solid var(--gray-200);border-radius:10px;padding:10px;">
                            <div style="font-size:.74rem;color:var(--gray-500);">Service Number</div>
                            <div style="font-weight:700;color:var(--gray-900);">{{ $application->user->service_number ?? '—' }}</div>
                        </div>
                        <div style="border:1px solid var(--gray-200);border-radius:10px;padding:10px;">
                            <div style="font-size:.74rem;color:var(--gray-500);">Report Period</div>
                            <div style="font-weight:700;color:var(--gray-900);">{{ $data['report_period'] ?? $data['period'] ?? '—' }}</div>
                        </div>
                        <div style="border:1px solid var(--gray-200);border-radius:10px;padding:10px;">
                            <div style="font-size:.74rem;color:var(--gray-500);">Category</div>
                            <div style="font-weight:700;color:var(--gray-900);text-transform:capitalize;">{{ $application->category }}</div>
                        </div>
                        <div style="border:1px solid var(--gray-200);border-radius:10px;padding:10px;">
                            <div style="font-size:.74rem;color:var(--gray-500);">Current Stage</div>
                            <div style="font-weight:700;color:var(--gray-900);">{{ $stageLabels[$application->workflow_stage] ?? ucwords(str_replace('_', ' ', $application->workflow_stage)) }}</div>
                        </div>
                        <div style="border:1px solid var(--gray-200);border-radius:10px;padding:10px;">
                            <div style="font-size:.74rem;color:var(--gray-500);">CGIS Unit</div>
                            <div style="font-weight:700;color:var(--gray-900);">{{ $application->user->assigned_cgis_unit_code ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

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
            <div class="redas-card preview-section {{ $loop->first ? 'active' : '' }}" style="margin-bottom:20px;" data-label="{{ $section['label'] }}">
                <div class="card-head">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:#ede9fe;color:#6d28d9;">
                            <i class="fas fa-table-list"></i>
                        </div>
                        {{ $section['label'] }}
                    </div>
                </div>
                <div class="card-body no-pad">
                    @if($section['html'])
                        <div style="padding:16px;">
                            {!! $section['html'] !!}
                        </div>
                    @else
                        <p style="padding:16px;color:#64748b;">No data entered for this section.</p>
                    @endif
                </div>
            </div>
            @empty
            <div class="redas-card preview-section active" style="margin-bottom:20px;" data-label="No Data">
                <div class="card-body">
                    <p style="padding:12px;color:#64748b;">No return data is available to preview.</p>
                </div>
            </div>
            @endforelse

            @include('partials.document-viewer', ['docRoute' => $documentRoute])
        </div>

        <div>
            <div class="redas-card" style="margin-bottom:20px;">
                <div class="card-head">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:#dcfce7;color:#15803d;">
                            <i class="fas fa-diagram-project"></i>
                        </div>
                        Process Flow
                    </div>
                </div>
                <div class="card-body">
                    @if($timeline->isEmpty())
                        <p style="font-size:.84rem;color:var(--gray-500);margin:0;">No workflow activity recorded yet.</p>
                    @else
                        <div style="display:flex;flex-direction:column;">
                            @foreach($timeline as $step)
                                @php
                                    $isRejected = ($step['action'] ?? '') === 'rejected';
                                    $dotColor = $isRejected ? '#dc2626' : ($loop->last ? 'var(--nis-600)' : '#94a3b8');
                                @endphp
                                <div style="display:flex;gap:12px;{{ $loop->last ? '' : 'padding-bottom:18px;' }}position:relative;">
                                    @unless($loop->last)
                                        <div style="position:absolute;left:7px;top:18px;bottom:0;width:2px;background:var(--gray-200);"></div>
                                    @endunless
                                    <div style="width:16px;height:16px;border-radius:50%;background:{{ $dotColor }};flex-shrink:0;margin-top:2px;position:relative;z-index:1;"></div>
                                    <div style="min-width:0;">
                                        <div style="font-size:.84rem;font-weight:700;color:var(--gray-900);">{{ $step['stage'] }}</div>
                                        <div style="font-size:.76rem;color:var(--gray-500);text-transform:capitalize;">
                                            {{ $step['action'] }} by {{ $step['by'] }}
                                        </div>
                                        @if($step['at'])
                                            <div style="font-size:.72rem;color:var(--gray-400);">{{ \Illuminate\Support\Carbon::parse($step['at'])->format('d M Y, H:i') }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="redas-card">
                <div class="card-head">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:#fee2e2;color:#b91c1c;">
                            <i class="fas fa-comment-dots"></i>
                        </div>
                        Reviewer Comments
                    </div>
                </div>
                <div class="card-body">
                    @if($commentHistory->isNotEmpty())
                        <div style="display:flex;flex-direction:column;gap:10px;">
                            @foreach($commentHistory as $entry)
                                @php
                                    [$badgeBg, $badgeColor, $badgeIcon] = match ($entry->action) {
                                        'approved' => ['#ecfdf5', '#15803d', 'fa-check'],
                                        'rejected' => ['#fef2f2', '#b91c1c', 'fa-undo'],
                                        'resubmitted', 'submitted' => ['#eff6ff', '#1d4ed8', 'fa-paper-plane'],
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
                    @elseif(filled($application->comments))
                        <div style="padding:12px;border:1px solid #fde68a;background:#fffbeb;border-radius:10px;color:#92400e;font-size:.84rem;">
                            {{ $application->comments }}
                        </div>
                    @else
                        <p style="font-size:.84rem;color:var(--gray-500);margin:0;">No comments have been added to this return.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</main>

@endsection
