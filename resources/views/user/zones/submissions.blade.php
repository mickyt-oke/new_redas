@include('partials.header')

<main class="redas-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">My Zonal Submissions</h1>
            <p class="page-subtitle">Track the status of all your zonal returns.</p>
        </div>
        <a href="{{ route('user.zones.returns.create') }}" class="btn-nis btn-primary-nis">
            <i class="fas fa-plus"></i> New Return
        </a>
    </div>

    @if (session('status'))
        <div style="background:#ecfdf5;border:1px solid #86efac;color:#166534;border-radius:12px;padding:12px 14px;margin-bottom:16px;">
            <i class="fas fa-check-circle" style="margin-right:6px;"></i>{{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div style="background:#fef2f2;border:1px solid #fca5a5;color:#991b1b;border-radius:12px;padding:12px 14px;margin-bottom:16px;">
            <i class="fas fa-exclamation-circle" style="margin-right:6px;"></i>{{ session('error') }}
        </div>
    @endif

    <div class="stats-grid animate-fade-up" style="margin-bottom:20px;">
        @php
        $subStats = [
            ['Total Submitted',    $stats['total'] ?? 0,    'fas fa-paper-plane',      'green',   'All time',          'up'],
            ['Pending Review',     $stats['pending'] ?? 0,  'fas fa-hourglass-half',   'gold',    'Awaiting review',   'neutral'],
            ['Approved',           $stats['approved'] ?? 0, 'fas fa-check-circle',     'green',   'Approved returns',  'up'],
            ['Returned for Action',$stats['returned'] ?? 0, 'fas fa-undo',             'warning', 'Action required',   'down'],
        ];
        @endphp
        @foreach($subStats as [$label, $val, $icon, $color, $change, $dir])
        <div class="stat-card {{ $color }}">
            <div class="stat-header"><span class="stat-label">{{ $label }}</span><span class="stat-icon"><i class="{{ $icon }}"></i></span></div>
            <div class="stat-value">{{ $val }}</div>
            <div class="stat-change {{ $dir }}"><i class="fas fa-{{ $dir === 'up' ? 'arrow-up' : ($dir === 'down' ? 'arrow-down' : 'minus') }}"></i> {{ $change }}</div>
        </div>
        @endforeach
    </div>

    <div class="redas-card animate-fade-up delay-2">
        <div class="card-body no-pad">
            @php
            $statusConfig = [
                'pending'  => ['badge-pending',  'Pending Review', 'fas fa-hourglass-half'],
                'approved' => ['badge-approved', 'Approved',       'fas fa-check-circle'],
                'returned' => ['badge-rejected', 'Returned',       'fas fa-undo'],
            ];
            $typeLabels = [
                'monthly'   => 'Monthly Return',
                'quarterly' => 'Quarterly Return',
                'biannual'  => 'Bi-Annual Return',
                'annual'    => 'Annual Return',
                'special'   => 'Special Report',
            ];
            @endphp
            <table class="redas-table" id="submissionsTable">
                <thead>
                    <tr>
                        <th>Period</th>
                        <th>Return Type</th>
                        <th>Command</th>
                        <th>Status</th>
                        <th>Workflow Stage</th>
                        <th>Submitted</th>
                        <th style="width:130px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $submission)
                    @php
                        $status = strtolower((string) $submission->status);
                        [$badgeClass, $statusLabel, $statusIcon] = $statusConfig[$status] ?? ['badge-draft', ucfirst($submission->status ?? 'Draft'), 'fas fa-circle'];
                        $data = is_array($submission->return_data) ? $submission->return_data : [];
                        $periodRaw = $data['period'] ?? $data['report_period'] ?? null;
                        $period = $periodRaw ?: ($submission->created_at?->format('M Y') ?? '—');
                        if ($periodRaw) {
                            try {
                                $period = \Carbon\Carbon::createFromFormat('Y-m', $periodRaw)->format('M Y');
                            } catch (\Throwable $e) {
                                $period = $periodRaw;
                            }
                        }
                        $type = $typeLabels[$data['return_type'] ?? ''] ?? ucfirst((string) ($data['return_type'] ?? '—'));
                        $command = $data['command_name'] ?? '—';
                        $stage = $submission->workflow_stage ? ucwords(str_replace('_', ' ', $submission->workflow_stage)) : '—';
                    @endphp
                    <tr class="sub-main-row" data-status="{{ $status }}">
                        <td><strong>{{ $period ?? '—' }}</strong></td>
                        <td style="font-size:.8rem;">{{ $type }}</td>
                        <td style="font-size:.78rem;color:var(--gray-600);">{{ $command }}</td>
                        <td>
                            <span class="status-badge {{ $badgeClass }}">
                                <i class="{{ $statusIcon }}" style="font-size:.65rem;"></i> {{ $statusLabel }}
                            </span>
                        </td>
                        <td style="font-size:.78rem;color:var(--gray-600);">{{ $stage }}</td>
                        <td style="font-size:.78rem;color:var(--gray-500);">{{ $submission->created_at?->format('d M Y') ?? '—' }}</td>
                        <td>
                            <div style="display:flex;gap:4px;">
                                <a href="{{ route('user.zones.returns.show', ['applicationHash' => \App\Services\HashidService::encode($submission->id)]) }}" class="btn-nis btn-ghost btn-sm" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($status !== 'approved')
                                <a href="{{ route('user.zones.returns.edit', ['applicationHash' => \App\Services\HashidService::encode($submission->id)]) }}" class="btn-nis btn-sm" style="background:var(--gold-50);border:1px solid var(--gold-300);color:var(--gold-700);padding:4px 8px;" title="Edit & Resubmit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ \Illuminate\Support\Facades\URL::signedRoute('user.zones.returns.destroy', ['applicationHash' => \App\Services\HashidService::encode($submission->id)]) }}" style="display:inline;" onsubmit="return confirm('Delete this return permanently? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-nis btn-ghost btn-sm" style="color:#b91c1c;" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:32px;color:var(--gray-500);font-size:.84rem;">
                            No submissions yet. Use <strong>New Return</strong> above to submit your first return.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($submissions->hasPages())
        <div class="card-body" style="border-top:1px solid var(--gray-100);">
            {{ $submissions->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</main>

@include('partials.footer')
