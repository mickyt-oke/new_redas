@include('partials.header')

@php
    $data = is_array($application->return_data) ? $application->return_data : [];
    $skipKeys = ['data_consent', 'report_period', 'reporting_officer', 'command_name', 'period', 'return_type', 'attachments'];
    $command = $data['command_name'] ?? $commandName ?? '—';
    $period = $data['report_period'] ?? $data['period'] ?? '—';
    $officer = $data['reporting_officer'] ?? optional($application->user)->name ?? '—';
    $typeLabels = ['monthly' => 'Monthly Return', 'quarterly' => 'Quarterly Return', 'biannual' => 'Bi-Annual Return', 'annual' => 'Annual Return', 'special' => 'Special Report'];
    $type = $typeLabels[$data['return_type'] ?? ''] ?? ucfirst((string) ($data['return_type'] ?? '—'));
    $status = strtolower((string) $application->status);
    $statusConfig = [
        'pending' => ['badge-pending', 'Pending Review'],
        'approved' => ['badge-approved', 'Approved'],
        'returned' => ['badge-rejected', 'Returned'],
        'rejected' => ['badge-rejected', 'Returned'],
    ];
    [$badgeClass, $statusLabel] = $statusConfig[$status] ?? ['badge-draft', ucfirst($application->status ?? 'Draft')];

    $sections = \App\Services\PreviewRenderer::buildSections($data, [
        'skipKeys' => $skipKeys,
        'sectionLabels' => [
            'personnel' => 'Personnel Strength',
            'operations' => 'Operations Summary',
            'logistics' => 'Logistics / Equipment',
            'finance' => 'Finance / Budget Summary',
            'challenges' => 'Challenges & Way Forward',
        ],
    ]);
@endphp

<main class="redas-content">
    <div class="page-header" style="margin-bottom:18px;">
        <div>
            <h1 class="page-title">Return Details</h1>
            <p class="page-subtitle">View the submitted return below.</p>
        </div>
        <a href="{{ route($backRoute) }}" class="btn-nis btn-ghost">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    @if (session('status'))
        <div style="background:#ecfdf5;border:1px solid #86efac;color:#166534;border-radius:12px;padding:12px 14px;margin-bottom:16px;">
            <i class="fas fa-check-circle" style="margin-right:6px;"></i>{{ session('status') }}
        </div>
    @endif

    <div class="redas-card" style="margin-bottom:20px;border-left:4px solid var(--nis-600);">
        <div class="card-body">
            <div style="display:flex;flex-wrap:wrap;gap:8px 28px;margin-bottom:12px;font-size:.85rem;">
                <span><strong>{{ $commandLabel ?? 'Command' }}:</strong> {{ $command }}</span>
                <span><strong>Report Period:</strong> {{ $period }}</span>
                <span><strong>Return Type:</strong> {{ $type }}</span>
                <span><strong>Reporting Officer:</strong> {{ $officer }}</span>
                <span><strong>Submitted:</strong> {{ optional($application->created_at)->format('d M Y, H:i') ?? '—' }}</span>
                <span><strong>Status:</strong> <span class="status-badge {{ $badgeClass }}">{{ $statusLabel }}</span></span>
            </div>
        </div>
    </div>

    @if($sections)
        @foreach($sections as $section)
        <div class="redas-card" style="margin-bottom:16px;">
            <div class="card-body">
                <h2 style="font-size:.95rem;font-weight:700;color:var(--nis-700);margin:0 0 12px;border-bottom:2px solid var(--nis-700);padding-bottom:6px;">{{ $section['label'] }}</h2>
                @if($section['html'])
                    {!! $section['html'] !!}
                @else
                    <p style="color:var(--gray-500);font-size:.84rem;">No data entered for this section.</p>
                @endif
            </div>
        </div>
        @endforeach
    @endif

    @if(!empty($data['attachments']))
    <div class="redas-card" style="margin-bottom:16px;">
        <div class="card-body">
            <h2 style="font-size:.95rem;font-weight:700;color:var(--nis-700);margin:0 0 12px;">Attachments</h2>
            <ul style="margin:0;padding-left:18px;font-size:.84rem;">
                @foreach((array) $data['attachments'] as $i => $path)
                    @if(is_string($path) && $path !== '')
                        <li><a href="{{ \Illuminate\Support\Facades\URL::signedRoute($docRoute ?? 'user.returns.document', ['applicationHash' => \App\Services\HashidService::encode($application->id), 'collection' => 'attachments', 'index' => $i]) }}" target="_blank">{{ basename($path) }}</a></li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px;">
        <a href="{{ route($backRoute) }}" class="btn-nis btn-ghost">Back to List</a>
    </div>
</main>

@include('partials.footer')
