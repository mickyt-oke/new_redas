@include('partials.header')

<main class="redas-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $commandName ?? 'Zonal' }} User Dashboard</h1>
            <p class="page-subtitle">
                Welcome back, <strong>{{ auth()->user()->name ?? 'Officer' }}</strong> —
                {{ now()->format('l, d F Y') }}
            </p>
        </div>
        <a href="{{ route('user.zones.returns.create') }}" class="btn-nis btn-primary-nis">
            <i class="fas fa-plus"></i> Submit Zonal Return
        </a>
    </div>

    <div style="background:linear-gradient(135deg,#fef3c7,#fde68a);border:1px solid #f59e0b;border-radius:var(--radius-md);padding:14px 18px;display:flex;align-items:center;gap:14px;margin-bottom:24px;" class="animate-fade-up">
        <div style="width:40px;height:40px;background:#f59e0b;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;color:white;flex-shrink:0;">
            <i class="fas fa-calendar-exclamation"></i>
        </div>
        <div style="flex:1;">
            <strong style="color:#92400e;font-size:.9rem;">Monthly Return Due — {{ now()->endOfMonth()->format('d F Y') }}</strong>
            <p style="color:#b45309;font-size:.8rem;margin:0;">Submit your {{ now()->format('F Y') }} zonal return before the deadline.</p>
        </div>
        <a href="{{ route('user.zones.returns.create') }}" style="background:#f59e0b;color:white;padding:8px 16px;border-radius:var(--radius-sm);font-size:.82rem;font-weight:700;text-decoration:none;flex-shrink:0;">
            Submit Now
        </a>
    </div>

    <div class="stats-grid animate-fade-up delay-1">
        <div class="stat-card green">
            <div class="stat-header"><span class="stat-label">Total Submitted</span><span class="stat-icon"><i class="fas fa-paper-plane"></i></span></div>
            <div class="stat-value" data-count="{{ $totalSubmissions }}">0</div>
            <div class="stat-change neutral"><i class="fas fa-minus"></i> All time</div>
        </div>
        <div class="stat-card gold">
            <div class="stat-header"><span class="stat-label">Pending Review</span><span class="stat-icon"><i class="fas fa-hourglass-half"></i></span></div>
            <div class="stat-value" data-count="{{ $pendingSubmissions }}">0</div>
            <div class="stat-change neutral"><i class="fas fa-minus"></i> Awaiting review</div>
        </div>
        <div class="stat-card green">
            <div class="stat-header"><span class="stat-label">Approved</span><span class="stat-icon"><i class="fas fa-check-circle"></i></span></div>
            <div class="stat-value" data-count="{{ $approvedSubmissions }}">0</div>
            <div class="stat-change up"><i class="fas fa-arrow-up"></i> Approved returns</div>
        </div>
        <div class="stat-card danger">
            <div class="stat-header"><span class="stat-label">Returned / Queried</span><span class="stat-icon"><i class="fas fa-exclamation-circle"></i></span></div>
            <div class="stat-value" data-count="{{ $queriedSubmissions }}">0</div>
            <div class="stat-change down"><i class="fas fa-arrow-down"></i> Action required</div>
        </div>
    </div>

    <div class="redas-card animate-fade-up delay-2" style="margin-top:20px;">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:var(--nis-50);color:var(--nis-600);"><i class="fas fa-inbox"></i></div>
                Recent Submissions
            </div>
            <a href="{{ route('user.zones.returns.index') }}" class="btn-nis btn-ghost btn-sm">View All</a>
        </div>
        <div class="card-body no-pad">
            @php
            $statusConfig = [
                'pending' => ['badge-pending', 'Pending Review'],
                'approved' => ['badge-approved', 'Approved'],
                'returned' => ['badge-rejected', 'Returned'],
                'rejected' => ['badge-rejected', 'Returned'],
            ];
            $typeLabels = ['monthly' => 'Monthly Return', 'quarterly' => 'Quarterly Return', 'biannual' => 'Bi-Annual Return', 'annual' => 'Annual Return', 'special' => 'Special Report'];
            @endphp
            <table class="redas-table">
                <thead>
                    <tr>
                        <th>Period</th>
                        <th>Return Type</th>
                        <th>Status</th>
                        <th>Stage</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $submission)
                    @php
                        $status = strtolower((string) $submission->status);
                        [$badgeClass, $statusLabel] = $statusConfig[$status] ?? ['badge-draft', ucfirst((string) ($submission->status ?? 'Draft'))];
                        $data = is_array($submission->return_data) ? $submission->return_data : [];
                        $periodRaw = $data['report_period'] ?? $data['period'] ?? null;
                        $period = $periodRaw ?: ($submission->created_at?->format('M Y') ?? '—');
                        if ($periodRaw) {
                            try { $period = \Carbon\Carbon::createFromFormat('Y-m', $periodRaw)->format('M Y'); } catch (\Throwable $e) { $period = $periodRaw; }
                        }
                        $type = $typeLabels[$data['return_type'] ?? ''] ?? ucfirst((string) ($data['return_type'] ?? '—'));
                        $stage = $submission->workflow_stage ? ucwords(str_replace('_', ' ', $submission->workflow_stage)) : '—';
                    @endphp
                    <tr>
                        <td><strong>{{ $period }}</strong></td>
                        <td style="font-size:.8rem;">{{ $type }}</td>
                        <td><span class="status-badge {{ $badgeClass }}">{{ $statusLabel }}</span></td>
                        <td><span style="display:inline-block;padding:2px 8px;border-radius:999px;font-size:.7rem;font-weight:600;background:var(--gray-100);color:var(--gray-600);">{{ $stage }}</span></td>
                        <td style="font-size:.78rem;color:var(--gray-500);">{{ $submission->created_at?->format('d M Y') ?? '—' }}</td>
                        <td>
                            <a href="{{ route('user.zones.returns.show', ['applicationHash' => \App\Services\HashidService::encode($submission->id)]) }}" class="btn-nis btn-ghost btn-sm" title="View Details"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:32px;color:var(--gray-500);font-size:.84rem;">
                            No submissions yet. Use <strong>Submit Zonal Return</strong> to file your first return.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="redas-card animate-fade-up delay-3" style="margin-top:20px;">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:var(--gold-100);color:var(--gold-500);"><i class="fas fa-bolt"></i></div>
                Quick Actions
            </div>
        </div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:10px;">
            <a href="{{ route('user.zones.returns.create') }}" class="btn-nis btn-primary-nis full-width"><i class="fas fa-plus-circle"></i> Submit Monthly Return</a>
            <a href="{{ route('user.zones.returns.index') }}" class="btn-nis btn-outline-nis full-width"><i class="fas fa-list"></i> My Submissions</a>
            <a href="{{ route('user.archive') }}" class="btn-nis btn-ghost full-width"><i class="fas fa-archive"></i> Archive Documents</a>
            <a href="{{ route('user.profile') }}" class="btn-nis btn-ghost full-width"><i class="fas fa-user-cog"></i> Update Profile</a>
        </div>
    </div>
</main>

@include('partials.footer')
