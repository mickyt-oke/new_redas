@php
    $submissions = $submissions ?? collect();
    $title = $title ?? 'Submissions';
    $status = $status ?? null;
    $emptyMessage = $emptyMessage ?? 'No submissions to display.';
    $showApproveReject = $showApproveReject ?? false;
    $documentRoute = $documentRoute ?? 'desk.admin.submissions.document';
    $previewRoute = $previewRoute ?? 'desk.admin.submissions.show';
    $pdfRoute = $pdfRoute ?? 'user.submissions.pdf';
    $downloadRoute = $downloadRoute ?? 'desk.admin.submissions.download';
    $approveRoute = $approveRoute ?? 'desk.admin.submissions.approve';
    $rejectRoute = $rejectRoute ?? 'desk.admin.submissions.reject';

    $hasAnyData = $submissions->isNotEmpty();
@endphp

<div class="redas-card" style="margin-bottom:20px;">
    <div class="card-head">
        <div class="card-head-title">
            <div class="card-head-icon" style="background:#ede9fe;color:#6d28d9;">
                <i class="fas fa-table-list"></i>
            </div>
            {{ $title }}
            @if($status)
                <span class="status-badge badge-{{ $status }}" style="margin-left:8px;text-transform:capitalize;">{{ $status }}</span>
            @endif
        </div>
    </div>
    <div class="card-body no-pad">
        @if($hasAnyData)
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered table-nis align-middle" style="min-width:820px;">
                    <thead>
                        <tr>
                            <th>Ref</th>
                            <th>Formation / Command</th>
                            <th>Officer</th>
                            <th>Period</th>
                            <th>Stage</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($submissions as $submission)
                            @php
                                $data = $submission->return_data ?? [];
                                $badge = match ($submission->status) {
                                    'approved' => 'badge-approved',
                                    'returned', 'rejected' => 'badge-rejected',
                                    default => 'badge-pending',
                                };
                            @endphp
                            <tr>
                                <td style="font-weight:600;">RET-{{ str_pad($submission->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $data['command_name'] ?? $submission->scope_code ?? '—' }}</td>
                                <td>
                                    {{ $data['reporting_officer'] ?? optional($submission->user)->name ?? '—' }}
                                    @if($submission->user?->service_number)
                                        <div style="font-size:.7rem;color:var(--gray-400);">{{ $submission->user->service_number }}</div>
                                    @endif
                                </td>
                                <td>{{ $data['report_period'] ?? '—' }}</td>
                                <td style="text-transform:capitalize;">{{ str_replace('_', ' ', $submission->workflow_stage) }}</td>
                                <td><span class="status-badge {{ $badge }}">{{ ucfirst($submission->status) }}</span></td>
                                <td>{{ optional($submission->created_at)->format('d M Y') }}</td>
                                <td style="text-align:right;">
                                    <div style="display:flex;gap:6px;justify-content:flex-end;flex-wrap:wrap;">
                                        <a href="{{ route($previewRoute, ['applicationHash' => \App\Services\HashidService::encode($submission->id)]) }}" class="btn-nis btn-sm btn-ghost" style="padding:5px 10px;font-size:.75rem;">
                                            <i class="fas fa-eye"></i> Preview
                                        </a>
                                        <a href="{{ URL::signedRoute($pdfRoute, ['applicationHash' => \App\Services\HashidService::encode($submission->id)]) }}" class="btn-nis btn-sm btn-outline-nis" style="padding:5px 10px;font-size:.75rem;" target="_blank">
                                            <i class="fas fa-file-pdf"></i> PDF
                                        </a>
                                        @if($showApproveReject)
                                            <form method="POST" action="{{ URL::signedRoute($approveRoute, ['applicationHash' => \App\Services\HashidService::encode($submission->id)]) }}" style="display:flex;gap:6px;flex-wrap:wrap;margin:0;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="text" name="note" maxlength="1000" placeholder="Approval note" style="padding:5px 8px;border:1px solid #cbd5e1;border-radius:6px;font-size:.72rem;min-width:120px;max-width:160px;">
                                                <button type="submit" class="btn-nis btn-sm btn-primary-nis" style="padding:5px 10px;font-size:.75rem;">Approve</button>
                                            </form>
                                            <form method="POST" action="{{ URL::signedRoute($rejectRoute, ['applicationHash' => \App\Services\HashidService::encode($submission->id)]) }}" style="display:flex;gap:6px;flex-wrap:wrap;margin:0;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="text" name="comment" maxlength="1000" required placeholder="Reason" style="padding:5px 8px;border:1px solid #cbd5e1;border-radius:6px;font-size:.72rem;min-width:120px;max-width:160px;">
                                                <button type="submit" class="btn-nis btn-sm" style="padding:5px 10px;font-size:.75rem;background:#b91c1c;color:#fff;">Return</button>
                                            </form>
                                        @else
                                            <a href="{{ URL::signedRoute($downloadRoute, ['applicationHash' => \App\Services\HashidService::encode($submission->id)]) }}" class="btn-nis btn-sm btn-outline-nis" style="padding:5px 10px;font-size:.75rem;">
                                                <i class="fas fa-download"></i> CSV
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                {{ $emptyMessage }}
            </div>
        @endif
    </div>
</div>
