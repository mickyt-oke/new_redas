@extends('desk-admin.layout')

@section('content')
<main class="redas-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">Submission Preview</h1>
            <p class="page-subtitle">Review the application details before approval or return.</p>
        </div>
        <a href="{{ route('user.desk.home') }}" class="btn-nis btn-ghost">
            <i class="fas fa-arrow-left"></i> Back to Review Dashboard
        </a>
    </div>

    <div class="stats-grid" style="margin-bottom:24px;">
        <div class="stat-card info">
            <div class="stat-header">
                <span class="stat-label">Submission</span>
                <span class="stat-icon"><i class="fas fa-hashtag"></i></span>
            </div>
            <div class="stat-value">#{{ $application->id }}</div>
            <div class="stat-change">{{ optional($application->created_at)->format('d M Y') }}</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-header">
                <span class="stat-label">Submitted By</span>
                <span class="stat-icon"><i class="fas fa-user-check"></i></span>
            </div>
            <div class="stat-value">{{ optional($application->user)->name ?? 'N/A' }}</div>
            <div class="stat-change">{{ optional($application->user)->primary_location_code ?? 'Unknown unit' }}</div>
        </div>
        <div class="stat-card gold">
            <div class="stat-header">
                <span class="stat-label">Status</span>
                <span class="stat-icon"><i class="fas fa-tag"></i></span>
            </div>
            <div class="stat-value" style="text-transform:capitalize;">{{ ucfirst($application->status) }}</div>
            <div class="stat-change">Current workflow review</div>
        </div>
        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-label">Review Stage</span>
                <span class="stat-icon"><i class="fas fa-eye"></i></span>
            </div>
            <div class="stat-value">Desk Admin</div>
            <div class="stat-change">Pending action</div>
        </div>
    </div>

    <div class="redas-card" style="margin-bottom:24px;">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:#e0f2fe;color:#0369a1;"><i class="fas fa-file-alt"></i></div>
                Submission Summary
            </div>
        </div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;">
                <div style="padding:14px;border:1px solid #e5e7eb;border-radius:12px;">
                    <div class="label-text">Application ID</div>
                    <div class="summary-value">#{{ $application->id }}</div>
                </div>
                <div style="padding:14px;border:1px solid #e5e7eb;border-radius:12px;">
                    <div class="label-text">Officer</div>
                    <div class="summary-value">{{ optional($application->user)->name ?? 'N/A' }}</div>
                </div>
                <div style="padding:14px;border:1px solid #e5e7eb;border-radius:12px;">
                    <div class="label-text">Status</div>
                    <div class="summary-value" style="text-transform:capitalize;">{{ ucfirst($application->status) }}</div>
                </div>
                <div style="padding:14px;border:1px solid #e5e7eb;border-radius:12px;">
                    <div class="label-text">Submitted</div>
                    <div class="summary-value">{{ optional($application->created_at)->format('d M Y, H:i') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="redas-card" style="margin-bottom:24px;">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:#eef2ff;color:#2563eb;"><i class="fas fa-info-circle"></i></div>
                Application Content
            </div>
            <a href="{{ route('user.desk.home') }}" class="btn-nis btn-ghost btn-sm">Return to dashboard</a>
        </div>
        <div class="card-body" style="display:grid;gap:18px;">
            @if(is_array($application->return_data) && count($application->return_data) > 0)
                @foreach($application->return_data as $section => $values)
                    <section style="border:1px solid #e5e7eb;border-radius:14px;background:#fff;padding:18px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:12px;">
                            <div style="font-size:.92rem;font-weight:700;color:#172554;">{{ ucfirst(str_replace('_', ' ', $section)) }}</div>
                            <span style="font-size:.78rem;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Section</span>
                        </div>
                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:14px;">
                            @foreach((array)$values as $key => $value)
                                <div style="padding:12px;border-radius:12px;background:#f8fafc;">
                                    <div style="font-size:.75rem;color:#64748b;margin-bottom:6px;">{{ ucfirst(str_replace('_', ' ', $key)) }}</div>
                                    <div style="font-size:.9rem;color:#0f172a;">{{ is_array($value) ? implode(', ', $value) : $value }}</div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            @else
                <div style="padding:18px;color:#64748b;">No return data is available to preview.</div>
            @endif
        </div>
    </div>

    <div class="redas-card" style="display:flex;justify-content:flex-end;gap:12px;flex-wrap:wrap;align-items:center;">
        <a href="{{ route('user.desk.home') }}" class="btn-nis btn-ghost">Back to dashboard</a>
        <form method="POST" action="{{ route($application->status === 'returned' ? 'desk.admin.submissions.reject' : 'desk.admin.submissions.approve', $application) }}" style="margin:0;">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn-nis btn-primary-nis">Continue review</button>
        </form>
    </div>
</main>
@endsection
