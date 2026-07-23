@include('partials.header')

<main class="redas-content">
    <div class="page-header" style="margin-bottom:18px;">
        <div>
            <h1 class="page-title" style="margin-bottom:4px;">
                <i class="{{ $directorate['icon'] }}" style="margin-right:8px;"></i>
                {{ $directorate['name'] }} Return
            </h1>
            <p class="page-subtitle">Unique reporting form based on the directorate reporting template.</p>
        </div>
        @if(auth()->user()?->role === 'directorate')
        <a href="{{ route('user.directorate.home') }}" class="btn-nis btn-ghost">
            <i class="fas fa-arrow-left"></i> Back to Directorates
        </a>
        @else
        <a href="{{ route('user.dashboard') }}" class="btn-nis btn-ghost">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        @endif
    </div>

    @if (session('status'))
        <div style="background:#ecfdf5;border:1px solid #86efac;color:#166534;border-radius:12px;padding:12px 14px;margin-bottom:16px;">
            <i class="fas fa-check-circle" style="margin-right:6px;"></i>{{ session('status') }}
        </div>
    @endif

    <div class="redas-card" style="margin-bottom:16px;">
        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:var(--nis-50);color:var(--nis-600);">
                    <i class="fas fa-sitemap"></i>
                </div>
                Directorate Overview
            </div>
        </div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:10px;">
                @foreach($allDirectorates as $dirSlug => $dir)
                    <a href="{{ route('user.directorates.show', $dirSlug) }}"
                       class="btn-nis {{ $slug === $dirSlug ? 'btn-primary-nis' : 'btn-ghost' }}"
                       style="justify-content:flex-start;">
                        <i class="{{ $dir['icon'] }}"></i>
                        {{ $dir['name'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('user.directorates.store', $slug) }}">
        @csrf

        <div class="redas-card" style="margin-bottom:14px;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:var(--gold-100);color:var(--gold-600);">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    Monthly Return
                </div>
            </div>
            <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Report Period</label>
                    <input type="month" class="ni" name="report_period" required value="{{ old('report_period', now()->format('Y-m')) }}">
                </div>
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Reporting Officer</label>
                    <input type="text" class="ni" name="reporting_officer" required value="{{ old('reporting_officer', auth()->user()->name) }}">
                </div>
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">Directorate</label>
                    <input type="text" class="ni" value="{{ $directorate['name'] }}" readonly>
                </div>
            </div>
        </div>

        @foreach($directorate['sections'] as $section)
            <div class="redas-card" style="margin-bottom:14px;">
                <div class="card-head">
                    <div class="card-head-title">
                        <div class="card-head-icon" style="background:#eff6ff;color:#1d4ed8;">
                            <i class="fas fa-list-check"></i>
                        </div>
                        {{ $section['title'] }}
                    </div>
                </div>
                <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;">
                    @foreach($section['fields'] as $field)
                        <div style="grid-column:{{ $field['type'] === 'textarea' ? '1 / -1' : 'auto' }};">
                            <label style="display:block;font-size:.8rem;font-weight:700;margin-bottom:6px;">{{ $field['label'] }}</label>

                            @if($field['type'] === 'textarea')
                                <textarea name="{{ $field['name'] }}" class="ni" rows="3" placeholder="Provide details..."></textarea>
                            @elseif($field['type'] === 'number')
                                <input type="number" min="0" name="{{ $field['name'] }}" class="ni" value="{{ old($field['name']) }}" placeholder="0">
                            @else
                                <input type="text" name="{{ $field['name'] }}" class="ni" value="{{ old($field['name']) }}" placeholder="Enter value">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="redas-card">
            <div class="card-body" style="display:flex;justify-content:flex-end;gap:10px;">
                <a href="{{ route('user.dashboard') }}" class="btn-nis btn-ghost">Cancel</a>
                <button type="submit" class="btn-nis btn-primary-nis">
                    <i class="fas fa-paper-plane"></i> Submit {{ $directorate['name'] }} Return
                </button>
            </div>
        </div>
    </form>
</main>

@include('partials.footer')
