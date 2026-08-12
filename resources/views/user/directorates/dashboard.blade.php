@include('partials.header')

<main class="redas-content">
    <div class="page-header" style="margin-bottom:18px;">
        <div>
            <h1 class="page-title" style="margin-bottom:4px;">
                <i class="{{ $directorate['icon'] ?? 'fas fa-building-columns' }}" style="margin-right:8px;"></i>
                {{ $directorate['name'] ?? 'Directorate' }} Dashboard
            </h1>
            <p class="page-subtitle">
                Welcome back, <strong>{{ auth()->user()?->name ?? 'Officer' }}</strong> —
                {{ now()->format('l, d F Y') }}
            </p>
        </div>
        @if($directorate && $slug)
        <a href="{{ route('user.directorates.show', ['slug' => $slug]) }}" class="btn-nis btn-primary-nis">
            <i class="fas fa-plus"></i> Submit {{ $directorate['name'] }} Return
        </a>
        @endif
    </div>

    @if (session('status'))
        <div style="background:#ecfdf5;border:1px solid #86efac;color:#166534;border-radius:12px;padding:12px 14px;margin-bottom:16px;">
            <i class="fas fa-check-circle" style="margin-right:6px;"></i>{{ session('status') }}
        </div>
    @endif

    @if (! $directorate)
        <div style="background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;border-radius:12px;padding:12px 14px;margin-bottom:16px;">
            <i class="fas fa-exclamation-triangle" style="margin-right:6px;"></i>
            Your account is not linked to a recognised directorate. Please contact the system administrator.
        </div>
    @endif

    <!-- Stats -->
    <div class="stats-grid animate-fade-up delay-1" style="margin-bottom:20px;">
        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-label">Total Submitted</span>
                <span class="stat-icon"><i class="fas fa-paper-plane"></i></span>
            </div>
            <div class="stat-value">{{ $totalSubmissions }}</div>
            <div class="stat-change up">All time</div>
        </div>
        <div class="stat-card gold">
            <div class="stat-header">
                <span class="stat-label">Pending Review</span>
                <span class="stat-icon"><i class="fas fa-hourglass-half"></i></span>
            </div>
            <div class="stat-value">{{ $pendingSubmissions }}</div>
            <div class="stat-change neutral">Awaiting supervisor</div>
        </div>
        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-label">Approved</span>
                <span class="stat-icon"><i class="fas fa-check-circle"></i></span>
            </div>
            <div class="stat-value">{{ $approvedSubmissions }}</div>
            <div class="stat-change up">Approved returns</div>
        </div>
        <div class="stat-card danger">
            <div class="stat-header">
                <span class="stat-label">Returned / Queried</span>
                <span class="stat-icon"><i class="fas fa-exclamation-circle"></i></span>
            </div>
            <div class="stat-value">{{ $queriedSubmissions }}</div>
            <div class="stat-change down">Action required</div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 340px;gap:20px;">
        <!-- Left column -->
        <div style="display:flex;flex-direction:column;gap:20px;">
            <!-- Directorate info -->
            <div class="redas-card animate-fade-up delay-2">
                <div class="card-head">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                            <i class="fas fa-building-columns"></i>
                        </div>
                        Directorate Information
                    </div>
                </div>
                <div class="card-body" style="font-size:.84rem;color:var(--gray-700);">
                    @if($directorate)
                        <p style="margin:0 0 8px;">
                            <strong>Directorate:</strong> {{ $directorate['name'] }}
                        </p>
                        <p style="margin:0 0 8px;">
                            <strong>Report Period:</strong> {{ now()->format('F Y') }}
                        </p>
                        <p style="margin:0;">
                            <strong>Reporting Officer:</strong> {{ auth()->user()?->name }}
                        </p>
                    @else
                        <p style="margin:0;">No directorate assignment found.</p>
                    @endif
                </div>
            </div>

            <!-- Recent submissions -->
            <div class="redas-card animate-fade-up delay-2">
                <div class="card-head">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:var(--nis-50);color:var(--nis-600);">
                            <i class="fas fa-inbox"></i>
                        </div>
                        Recent Submissions
                    </div>
                    @if($directorate && $slug)
                    <a href="{{ route('user.directorates.show', ['slug' => $slug]) }}" class="btn-nis btn-ghost btn-sm">Submit New</a>
                    @endif
                </div>
                <div class="card-body no-pad">
                    <table class="redas-table searchable-table">
                        <thead>
                            <tr>
                                <th>Period</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($submissions as $submission)
                                @php
                                    $status = strtolower((string) $submission->status);
                                    $period = $submission->return_data['report_period'] ?? $submission->created_at?->format('F Y') ?? '—';
                                @endphp
                                <tr>
                                    <td><strong>{{ $period }}</strong></td>
                                    <td>
                                        <span class="status-badge
                                            @if($status === 'approved') badge-approved
                                            @elseif(in_array($status, ['pending', 'submitted'])) badge-pending
                                            @elseif(in_array($status, ['rejected', 'queried', 'returned'])) badge-rejected
                                            @else badge-draft @endif">
                                            {{ ucfirst($submission->status ?? 'Draft') }}
                                        </span>
                                    </td>
                                    <td style="font-size:.78rem;color:var(--gray-500);">{{ $submission->created_at?->format('d M Y') ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="text-align:center;padding:24px;color:var(--gray-500);font-size:.84rem;">
                                        No submissions yet. Use the button above to submit your first return.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right column -->
        <div style="display:flex;flex-direction:column;gap:20px;">
            <!-- Notifications -->
            <div class="redas-card animate-fade-up delay-3">
                <div class="card-head">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:#fef9c3;color:#a16207;">
                            <i class="fas fa-bell"></i>
                        </div>
                        Notifications
                    </div>
                    @if($unreadNotifications > 0)
                        <span class="link-badge danger">{{ $unreadNotifications }}</span>
                    @endif
                </div>
                <div class="card-body" style="font-size:.84rem;color:var(--gray-700);">
                    @if($unreadNotifications > 0)
                        <p style="margin:0 0 10px;">You have <strong>{{ $unreadNotifications }}</strong> unread notification{{ $unreadNotifications === 1 ? '' : 's' }}.</p>
                    @else
                        <p style="margin:0 0 10px;">No new notifications.</p>
                    @endif
                    <a href="#" onclick="document.getElementById('notifBtn')?.click(); return false;" style="color:var(--nis-600);font-weight:600;text-decoration:none;font-size:.8rem;">
                        View all <i class="fas fa-arrow-right" style="font-size:.7rem;"></i>
                    </a>
                </div>
            </div>

            <!-- Quick actions -->
            <div class="redas-card animate-fade-up delay-4">
                <div class="card-head">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:var(--gold-100);color:var(--gold-500);">
                            <i class="fas fa-bolt"></i>
                        </div>
                        Quick Actions
                    </div>
                </div>
                <div class="card-body" style="display:flex;flex-direction:column;gap:10px;">
                    @if($directorate && $slug)
                        <a href="{{ route('user.directorates.show', ['slug' => $slug]) }}" class="btn-nis btn-primary-nis" style="justify-content:center;">
                            <i class="fas fa-file-signature"></i> Submit Monthly Return
                        </a>
                    @endif
                    <a href="{{ route('user.directorates.home') }}" class="btn-nis btn-ghost" style="justify-content:center;">
                        <i class="fas fa-building-columns"></i> Directorate Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

@include('partials.footer')
