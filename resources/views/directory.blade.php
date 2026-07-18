<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="NIS-REDAS Directory — Nigeria Immigration Service offices, passport centres, border posts, airports, seaports, and foreign missions.">
    <title>NIS-REDAS | Directory</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|inter:400,500,600,700,800" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/nis.png') }}">
    @include('partials.head-meta')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @laravelPWA

    <style>
        :root { --dir-green: #006633; --dir-green-dark: #004d26; --dir-gold: #c5922a; }
        .directory-page { background: #f8fafc; min-height: 100vh; }
        .dir-nav { background: linear-gradient(135deg, #002d14, #006633); position: sticky; top: 0; z-index: 1030; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .dir-nav .nav-container { max-width: 1200px; margin: 0 auto; padding: 16px 24px; display: flex; align-items: center; justify-content: space-between; }
        .dir-nav .nav-brand { display: flex; align-items: center; gap: 10px; color: white; text-decoration: none; font-weight: 700; font-size: 1.1rem; }
        .dir-nav .nav-brand img { height: 36px; }
        .dir-nav .nav-links { display: flex; align-items: center; gap: 24px; }
        .dir-nav .nav-link-item { color: rgba(255,255,255,0.85); text-decoration: none; font-size: 0.88rem; font-weight: 500; transition: color .2s; }
        .dir-nav .nav-link-item:hover { color: white; }
        .dir-nav .nav-cta { background: var(--dir-gold); color: #1a1a1a; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.85rem; transition: transform .2s; }
        .dir-nav .nav-cta:hover { transform: translateY(-1px); }
        .dir-hero { background: linear-gradient(135deg, #002d14, #006633); color: white; padding: 56px 24px 40px; text-align: center; }
        .dir-hero h1 { font-size: clamp(1.8rem, 4vw, 2.6rem); font-weight: 800; margin-bottom: 10px; }
        .dir-hero p { color: rgba(255,255,255,0.8); font-size: 1rem; max-width: 680px; margin: 0 auto 24px; }
        .dir-search-wrap { max-width: 700px; margin: 0 auto 24px; position: relative; }
        .dir-search-wrap input { width: 100%; padding: 16px 20px 16px 48px; border: none; border-radius: 12px; font-size: 1rem; box-shadow: 0 8px 24px rgba(0,0,0,0.15); }
        .dir-search-wrap i { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 1.1rem; }
        .dir-preview { max-width: 900px; margin: 0 auto 24px; background: white; border-radius: 16px; box-shadow: 0 4px 16px rgba(0,0,0,0.1); overflow: hidden; transition: all .3s ease; }
        .dir-preview-header { padding: 12px 16px; background: #f0faf4; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; }
        .dir-preview-title { font-size: 0.95rem; font-weight: 700; color: #0f172a; margin: 0; }
        .dir-preview-placeholder { padding: 32px; text-align: center; color: #64748b; }
        .dir-preview-placeholder i { font-size: 2.5rem; color: #cbd5e1; margin-bottom: 12px; }
        .dir-preview-map { display: none; }
        .dir-preview-map iframe { width: 100%; height: 320px; border: 0; }
        .dir-preview.active .dir-preview-placeholder { display: none; }
        .dir-preview.active .dir-preview-map { display: block; }
        .dir-main { max-width: 1200px; margin: 0 auto; padding: 0 24px 48px; }
        .dir-tabs { display: flex; gap: 8px; overflow-x: auto; padding-bottom: 8px; margin-bottom: 20px; scrollbar-width: thin; }
        .dir-tabs::-webkit-scrollbar { height: 6px; }
        .dir-tabs::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .dir-tab { flex: 0 0 auto; background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px 16px; color: #475569; text-decoration: none; font-size: 0.85rem; font-weight: 600; transition: all .2s; white-space: nowrap; }
        .dir-tab:hover { border-color: var(--dir-green); color: var(--dir-green); }
        .dir-tab.active { background: var(--dir-green); border-color: var(--dir-green); color: white; }
        .dir-tab .count { font-size: 0.72rem; opacity: 0.8; margin-left: 4px; }
        .dir-section { background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); overflow: hidden; }
        .dir-section-header { padding: 18px 24px; border-bottom: 1px solid #e2e8f0; }
        .dir-section-title { font-size: 1.15rem; font-weight: 700; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 10px; }
        .dir-section-title i { color: var(--dir-green); }
        .dir-table-wrap { overflow-x: auto; }
        .dir-table { width: 100%; margin-bottom: 0; font-size: 0.88rem; }
        .dir-table thead th { background: #f8fafc; color: #334155; font-weight: 700; padding: 12px 16px; border-bottom: 2px solid #e2e8f0; white-space: nowrap; }
        .dir-table tbody td { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; color: #475569; vertical-align: middle; }
        .dir-table tbody tr:hover td { background: #f8fafc; }
        .dir-table .address-cell { max-width: 320px; line-height: 1.5; }
        .dir-empty { padding: 40px; text-align: center; color: #64748b; }
        .dir-map-btn { background: var(--dir-green); color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 0.78rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: background .2s; }
        .dir-map-btn:hover { background: var(--dir-green-dark); }
        .dir-footer { background: #0f172a; color: rgba(255,255,255,0.7); padding: 40px 24px; font-size: 0.85rem; margin-top: 40px; }
        .dir-footer-inner { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
        .dir-footer a { color: rgba(255,255,255,0.7); text-decoration: none; }
        .dir-footer a:hover { color: white; }
        .dir-mobile-toggle { display: none; background: rgba(255,255,255,0.1); border: none; width: 40px; height: 40px; border-radius: 8px; color: white; cursor: pointer; }
        .pagination { justify-content: center; margin: 24px 0 8px; }
        .page-link { color: var(--dir-green); }
        .page-item.active .page-link { background-color: var(--dir-green); border-color: var(--dir-green); }
        @media (max-width: 768px) {
            .dir-nav .nav-links { display: none; position: absolute; top: 70px; left: 0; right: 0; background: #002d14; flex-direction: column; padding: 16px 24px; gap: 12px; }
            .dir-nav .nav-links.open { display: flex; }
            .dir-mobile-toggle { display: flex; align-items: center; justify-content: center; }
            .dir-hero { padding: 44px 20px 32px; }
            .dir-main { padding: 0 16px 40px; }
            .dir-tab { padding: 8px 12px; font-size: 0.8rem; }
            .dir-section-header { padding: 14px 16px; }
            .dir-table { font-size: 0.82rem; }
            .dir-preview-map iframe { height: 240px; }
        }
    </style>
</head>
<body class="directory-page">

@include('partials.preloader')

<!-- Navigation -->
<nav class="dir-nav">
    <div class="nav-container">
        <a href="{{ url('/') }}" class="nav-brand">
            <img src="{{ asset('assets/images/nis-logo.png') }}" alt="NIS Logo">
            <span>NIS&nbsp;REDAS</span>
        </a>
        <div class="nav-links" id="dirNavLinks">
            <a href="{{ url('/') }}" class="nav-link-item">Home</a>
            <a href="{{ route('directory') }}" class="nav-link-item">Directory</a>
            <a href="{{ route('login') }}" class="nav-cta"><i class="fas fa-sign-in-alt"></i> Access Portal</a>
        </div>
        <button class="dir-mobile-toggle" id="dirMobileToggle" aria-label="Toggle navigation"><i class="fas fa-bars"></i></button>
    </div>
</nav>

<!-- Hero + Search -->
<section class="dir-hero">
    <h1>NIS Directory</h1>
    <p>Locate Nigeria Immigration Service offices, passport issuing centres, border posts, airports, marine commands, training institutions, and foreign missions.</p>
    <form method="GET" action="{{ route('directory') }}" class="dir-search-wrap">
        <i class="fas fa-search"></i>
        <input type="text" name="search" id="directorySearch" value="{{ $search }}" placeholder="Search by name, state, address or region..." autocomplete="off">
        <input type="hidden" name="tab" value="{{ $activeTab }}">
    </form>
</section>

<!-- Location Preview Section -->
<section class="dir-preview" id="locationPreview">
    <div class="dir-preview-header">
        <h2 class="dir-preview-title"><i class="fas fa-map-marked-alt me-2"></i>Location Preview</h2>
        <span class="text-muted" style="font-size: 0.78rem;">Select an entry to view on map</span>
    </div>
    <div class="dir-preview-placeholder">
        <i class="fas fa-map-marked-alt"></i>
        <p>Click "View on Map" on any directory entry to preview its location here.</p>
    </div>
    <div class="dir-preview-map">
        <h5 id="previewTitle" style="padding: 12px 16px; margin: 0; border-bottom: 1px solid #e2e8f0; font-size: 0.95rem;"></h5>
        <iframe id="previewFrame" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</section>

<!-- Main content -->
<main class="dir-main">

    <!-- Tabs -->
    <nav class="dir-tabs" aria-label="Directory categories">
        @foreach($categories as $key => $label)
        <a href="{{ route('directory', ['tab' => $key, 'search' => $search]) }}" class="dir-tab {{ $activeTab === $key ? 'active' : '' }}">
            {{ $label }}
            <span class="count">({{ $counts[$key] }})</span>
        </a>
        @endforeach
    </nav>

    <!-- Active Category Table -->
    <section class="dir-section">
        <div class="dir-section-header">
            <h2 class="dir-section-title">
                <i class="fas {{ $icons[$activeTab] }}"></i>
                {{ $categories[$activeTab] }}
            </h2>
        </div>
        <div class="dir-table-wrap">
            <table class="dir-table">
                <thead>
                    <tr>
                        <th>S/N</th>
                        @if($activeTab === 'foreign_mission')
                            <th>Region</th>
                            <th>Country</th>
                            <th>City</th>
                            <th>Type</th>
                        @elseif($activeTab === 'airport')
                            <th>Airport</th>
                            <th>Code</th>
                            <th>State</th>
                        @else
                            <th>Name</th>
                            @if($activeTab !== 'zonal')
                            <th>State</th>
                            @endif
                        @endif
                        <th class="address-cell">Address</th>
                        <th>Map</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $i => $item)
                    <tr>
                        <td>{{ $items->firstItem() + $i }}</td>
                        @if($activeTab === 'foreign_mission')
                            <td>{{ $item->region }}</td>
                            <td><strong>{{ $item->country }}</strong></td>
                            <td>{{ $item->city }}</td>
                            <td>{{ $item->type }}</td>
                            <td class="address-cell">{{ $item->address }}</td>
                        @elseif($activeTab === 'airport')
                            <td><strong>{{ $item->name }}</strong></td>
                            <td>{{ $item->code }}</td>
                            <td>{{ $item->state }}</td>
                            <td class="address-cell">{{ $item->address }}<br><small class="text-muted">{{ $item->email }}</small></td>
                        @else
                            <td><strong>{{ $item->name }}</strong></td>
                            @if($activeTab !== 'zonal')
                            <td>{{ $item->state }}</td>
                            @endif
                            <td class="address-cell">{{ $item->address }}<br><small class="text-muted">{{ $item->email }}</small></td>
                        @endif
                        <td>
                            <button class="dir-map-btn" data-address="{{ $item->address }}">
                                <i class="fas fa-map-marker-alt"></i> View
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="dir-empty">
                            <i class="fas fa-search" style="font-size: 1.5rem; color: #cbd5e1; margin-bottom: 12px; display: block;"></i>
                            No entries found{{ $search ? ' for "'.$search.'"' : '' }}.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
        <div class="d-flex justify-content-center p-3">
            {{ $items->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </section>

</main>

<!-- Footer -->
<footer class="dir-footer">
    <div class="dir-footer-inner">
        <div>
            <strong>Nigeria Immigration Service</strong><br>
            <span>Service Headquarters, Airport Road, Sauka, Abuja — FCT</span>
        </div>
        <div>
            <a href="https://www.immigration.gov.ng" target="_blank" rel="noopener"><i class="fas fa-globe me-1"></i>immigration.gov.ng</a>
        </div>
        <div style="text-align: right;">
            &copy; {{ date('Y') }} NIS-REDAS v2.0 — ICT Directorate
        </div>
    </div>
</footer>

<script>
(function() {
    'use strict';

    const preview = document.getElementById('locationPreview');
    const previewTitle = document.getElementById('previewTitle');
    const previewFrame = document.getElementById('previewFrame');
    const searchInput = document.getElementById('directorySearch');
    const mobileToggle = document.getElementById('dirMobileToggle');
    const navLinks = document.getElementById('dirNavLinks');

    // Debounce search input submission
    let searchTimer;
    searchInput?.addEventListener('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => this.form.submit(), 450);
    });

    // Map preview buttons
    document.querySelectorAll('.dir-map-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const address = btn.dataset.address || '';
            previewTitle.textContent = address;
            previewFrame.src = 'https://maps.google.com/maps?q=' + encodeURIComponent(address) + '&t=&z=15&ie=UTF8&iwloc=&output=embed';
            preview.classList.add('active');
            preview.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
    });

    // Mobile nav toggle
    mobileToggle?.addEventListener('click', () => navLinks?.classList.toggle('open'));
})();
</script>

</body>
</html>
