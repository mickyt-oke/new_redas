@php
    $submissions = $submissions ?? collect();
    $documentRoute = $documentRoute ?? 'desk.admin.submissions.document';
    $showApproveReject = $showApproveReject ?? false;
    $actions = $actions ?? null;

    $hasFormation = false;
    $hasOfficer = false;
    $hasPeriod = false;
    $hasStage = false;
    $hasStatus = false;
    $hasSubmitted = false;

    foreach ($submissions as $submission) {
        $data = $submission->return_data ?? [];
        if (!empty($data['command_name']) || !empty($submission->scope_code)) $hasFormation = true;
        if (!empty($data['reporting_officer']) || !empty(optional($submission->user)->name)) $hasOfficer = true;
        if (!empty($data['report_period'])) $hasPeriod = true;
        if (!empty($submission->workflow_stage)) $hasStage = true;
        if (!empty($submission->status)) $hasStatus = true;
        if (!empty($submission->created_at)) $hasSubmitted = true;
    }
@endphp

<div class="redas-card">
    <div class="card-head">
        <div class="card-head-title">
            <div class="card-head-icon" style="background:#ede9fe;color:#6d28d9;">
                <i class="fas fa-list-check"></i>
            </div>
            {{ $title }}
        </div>
        @if(!empty($status))
            @php
                $badgeClass = match (strtolower((string) $status)) {
                    'approved' => 'badge-approved',
                    'returned', 'rejected' => 'badge-rejected',
                    default => 'badge-pending',
                };
            @endphp
            <span class="status-badge {{ $badgeClass }}">{{ ucfirst($status) }}</span>
        @endif
        @if(is_string($actions) && $actions !== '')
            {!! $actions !!}
        @endif
    </div>
    <div class="card-body no-pad">
        @if($submissions->isEmpty())
            <div style="padding:24px;text-align:center;color:var(--gray-500);font-size:.85rem;">
                {{ $emptyMessage ?? 'No submissions found.' }}
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered table-nis align-middle" style="min-width:760px;margin-bottom:0;">
                    <thead>
                        <tr>
                            <th>Ref</th>
                            @if($hasFormation)<th>Formation / Command</th>@endif
                            @if($hasOfficer)<th>Officer</th>@endif
                            @if($hasPeriod)<th>Period</th>@endif
                            @if($hasStage)<th>Stage</th>@endif
                            @if($hasStatus)<th>Status</th>@endif
                            @if($hasSubmitted)<th>Submitted</th>@endif
                            <th style="min-width:220px;">Actions</th>
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
                                @if($hasFormation)<td>{{ $data['command_name'] ?? $submission->scope_code ?? '—' }}</td>@endif
                                @if($hasOfficer)<td>{{ $data['reporting_officer'] ?? optional($submission->user)->name ?? '—' }}</td>@endif
                                @if($hasPeriod)<td>{{ $data['report_period'] ?? '—' }}</td>@endif
                                @if($hasStage)<td style="text-transform:capitalize;">{{ str_replace('_', ' ', $submission->workflow_stage) }}</td>@endif
                                @if($hasStatus)<td><span class="status-badge {{ $badge }}">{{ ucfirst($submission->status) }}</span></td>@endif
                                @if($hasSubmitted)<td>{{ optional($submission->created_at)->format('d M Y') }}</td>@endif
                                <td>
                                    <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
                                        <a href="{{ route($documentRoute, ['applicationHash' => \App\Services\HashidService::encode($submission->id)]) }}" class="btn-nis btn-sm btn-ghost">
                                            <i class="fas fa-eye"></i> Preview
                                        </a>
                                        <a href="{{ URL::signedRoute('user.submissions.pdf', ['applicationHash' => \App\Services\HashidService::encode($submission->id)]) }}" class="btn-nis btn-sm btn-outline-nis" target="_blank">
                                            <i class="fas fa-file-pdf"></i> PDF
                                        </a>
                                        <a href="{{ URL::signedRoute('desk.admin.submissions.download', ['applicationHash' => \App\Services\HashidService::encode($submission->id)]) }}" class="btn-nis btn-sm btn-outline-nis">
                                            <i class="fas fa-download"></i> Download
                                        </a>

                                        @if($showApproveReject)
                                            <form method="POST" action="{{ URL::signedRoute($approveRoute, ['applicationHash' => \App\Services\HashidService::encode($submission->id)]) }}" style="display:flex;gap:6px;flex-wrap:wrap;margin:0;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="text" name="note" maxlength="1000" placeholder="Approval note" style="padding:5px 8px;border:1px solid var(--gray-300);border-radius:6px;font-size:.75rem;min-width:120px;max-width:160px;">
                                                <button type="submit" class="btn-nis btn-sm btn-primary-nis"><i class="fas fa-check"></i></button>
                                            </form>
                                            <form method="POST" action="{{ URL::signedRoute($rejectRoute, ['applicationHash' => \App\Services\HashidService::encode($submission->id)]) }}" style="display:flex;gap:6px;flex-wrap:wrap;margin:0;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="text" name="comment" maxlength="1000" required placeholder="Return reason" style="padding:5px 8px;border:1px solid var(--gray-300);border-radius:6px;font-size:.75rem;min-width:120px;max-width:160px;">
                                                <button type="submit" class="btn-nis btn-sm" style="background:var(--color-danger);color:#fff;"><i class="fas fa-undo"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
