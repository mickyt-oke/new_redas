@extends('admin.headquarters.layout')

@section('title', 'Return Detail')

@section('content')

@php
    $data = $application->return_data ?? [];
    $skipKeys = ['directorate_slug', 'data_consent', 'report_period', 'reporting_officer', 'command_name', 'supporting_documents', 'attachments'];
    $badge = match ($application->status) {
        'approved' => 'badge-approved',
        'returned', 'rejected' => 'badge-rejected',
        default => 'badge-pending',
    };

    $documentGroups = [];
    foreach (['supporting_documents' => ['supporting', 'Supporting Documents'], 'attachments' => ['attachments', 'Attachments']] as $key => [$collection, $label]) {
        $paths = array_values(array_filter((array) ($data[$key] ?? [])));
        if ($paths !== []) {
            $documentGroups[] = [$collection, $label, $paths];
        }
    }
@endphp

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
            <a href="{{ route('admin.hq.returns') }}" class="btn-nis btn-ghost">
                <i class="fas fa-arrow-left"></i> Back to Returns
            </a>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:minmax(0,2fr) minmax(0,1fr);gap:20px;align-items:start;">
        <div>
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
                            <div style="font-weight:700;color:var(--gray-900);">{{ $data['report_period'] ?? '—' }}</div>
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

            <div class="redas-card" style="margin-bottom:20px;">
                <div class="card-head">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:#ede9fe;color:#6d28d9;">
                            <i class="fas fa-table-list"></i>
                        </div>
                        Return Data
                    </div>
                </div>
                <div class="card-body no-pad">
                    <div style="overflow:auto;">
                        <table class="redas-table" style="min-width:520px;">
                            <tbody>
                                @forelse(collect($data)->except($skipKeys) as $field => $value)
                                    <tr>
                                        <td style="font-weight:600;width:40%;text-transform:capitalize;">{{ str_replace('_', ' ', $field) }}</td>
                                        <td>{{ is_array($value) ? implode(', ', array_map('strval', $value)) : $value }}</td>
                                    </tr>
                                @empty
                                    <tr><td style="text-align:center;color:var(--gray-400);padding:24px;">No additional return data recorded.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="redas-card">
                <div class="card-head">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:#fef3c7;color:#b45309;">
                            <i class="fas fa-paperclip"></i>
                        </div>
                        Documents
                    </div>
                </div>
                <div class="card-body">
                    @if($documentGroups === [])
                        <p style="font-size:.84rem;color:var(--gray-500);margin:0;">No documents or images were uploaded with this return.</p>
                    @else
                        @foreach($documentGroups as [$collection, $label, $paths])
                            <div style="margin-bottom:14px;">
                                <div style="font-size:.78rem;font-weight:700;color:var(--gray-600);margin-bottom:8px;">{{ $label }}</div>
                                <div style="display:flex;flex-direction:column;gap:6px;">
                                    @foreach($paths as $i => $path)
                                        <a href="{{ route('admin.hq.returns.document', [$application, $collection, $i]) }}" target="_blank" style="display:flex;align-items:center;gap:10px;padding:8px 12px;border:1px solid var(--gray-200);border-radius:8px;text-decoration:none;color:var(--gray-700);font-size:.84rem;">
                                            <i class="fas fa-file-arrow-down" style="color:var(--nis-600);"></i>
                                            <span>{{ basename($path) }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
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
                    @if(filled($application->comments))
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
