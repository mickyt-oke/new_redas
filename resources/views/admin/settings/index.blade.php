@include('partials.header3')

<main class="redas-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">System Settings</h1>
            <p class="page-subtitle">
                Configure ABAC, geolocation and global platform behaviour.
            </p>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('admin.audit-log.index') }}" class="btn-nis btn-ghost">
                <i class="fas fa-shield-alt"></i> Audit Log
            </a>
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success" style="margin-bottom:20px;padding:12px 16px;border-radius:var(--radius-md);background:#dcfce7;color:#15803d;border:1px solid #86efac;">
            {{ session('status') }}
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST" class="redas-card">
        @csrf
        @method('PUT')

        <div class="card-head">
            <div class="card-head-title">
                <div class="card-head-icon" style="background:var(--nis-50);color:var(--nis-600);"><i class="fas fa-shield-alt"></i></div>
                ABAC / Geolocation Access Control
            </div>
            <button type="submit" class="btn-nis btn-primary-nis btn-sm">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>

        <div class="card-body" style="display:flex;flex-direction:column;gap:20px;">
            <div class="grid-2" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
                <div>
                    <label class="form-label" style="font-weight:600;font-size:.85rem;color:var(--gray-700);display:block;margin-bottom:6px;">
                        ABAC Enabled
                    </label>
                    <select name="abac_enabled" class="form-select" style="width:100%;padding:8px 12px;border-radius:var(--radius-md);border:1px solid var(--gray-200);">
                        <option value="1" {{ ($settings['abac_enabled'] ?? true) ? 'selected' : '' }}>Enabled</option>
                        <option value="0" {{ !($settings['abac_enabled'] ?? true) ? 'selected' : '' }}>Disabled</option>
                    </select>
                    <small style="color:var(--gray-400);">Master switch for attribute-based access control.</small>
                </div>

                <div>
                    <label class="form-label" style="font-weight:600;font-size:.85rem;color:var(--gray-700);display:block;margin-bottom:6px;">
                        Location Enforcement
                    </label>
                    <select name="abac_location_enforcement" class="form-select" style="width:100%;padding:8px 12px;border-radius:var(--radius-md);border:1px solid var(--gray-200);">
                        <option value="1" {{ ($settings['abac_location_enforcement'] ?? true) ? 'selected' : '' }}>Enforced</option>
                        <option value="0" {{ !($settings['abac_location_enforcement'] ?? true) ? 'selected' : '' }}>Not enforced</option>
                    </select>
                    <small style="color:var(--gray-400);">Block login and requests from outside the user's registered state.</small>
                </div>

                <div>
                    <label class="form-label" style="font-weight:600;font-size:.85rem;color:var(--gray-700);display:block;margin-bottom:6px;">
                        HQ Bypass
                    </label>
                    <select name="abac_hq_bypass" class="form-select" style="width:100%;padding:8px 12px;border-radius:var(--radius-md);border:1px solid var(--gray-200);">
                        <option value="1" {{ ($settings['abac_hq_bypass'] ?? false) ? 'selected' : '' }}>Allowed</option>
                        <option value="0" {{ !($settings['abac_hq_bypass'] ?? false) ? 'selected' : '' }}>Not allowed</option>
                    </select>
                    <small style="color:var(--gray-400);">Allow HQ/admin users to authenticate from any location.</small>
                </div>

                <div>
                    <label class="form-label" style="font-weight:600;font-size:.85rem;color:var(--gray-700);display:block;margin-bottom:6px;">
                        Allowed Countries
                    </label>
                    <input type="text" name="abac_allowed_countries" value="{{ $settings['abac_allowed_countries'] ?? 'Nigeria' }}" class="form-control" style="width:100%;padding:8px 12px;border-radius:var(--radius-md);border:1px solid var(--gray-200);">
                    <small style="color:var(--gray-400);">Comma-separated list of country names (e.g. Nigeria).</small>
                </div>

                <div>
                    <label class="form-label" style="font-weight:600;font-size:.85rem;color:var(--gray-700);display:block;margin-bottom:6px;">
                        Geolocation Provider
                    </label>
                    <select name="geolocation_provider" class="form-select" style="width:100%;padding:8px 12px;border-radius:var(--radius-md);border:1px solid var(--gray-200);">
                        <option value="ip-api" {{ ($settings['geolocation_provider'] ?? 'ip-api') === 'ip-api' ? 'selected' : '' }}>ip-api.com (free)</option>
                        <option value="ipapi" {{ ($settings['geolocation_provider'] ?? 'ip-api') === 'ipapi' ? 'selected' : '' }}>ipapi.co</option>
                    </select>
                    <small style="color:var(--gray-400);">Service used to resolve IP addresses to locations.</small>
                </div>

                <div>
                    <label class="form-label" style="font-weight:600;font-size:.85rem;color:var(--gray-700);display:block;margin-bottom:6px;">
                        Cache TTL (minutes)
                    </label>
                    <input type="number" name="geolocation_cache_ttl" value="{{ $settings['geolocation_cache_ttl'] ?? 1440 }}" min="0" max="10080" class="form-control" style="width:100%;padding:8px 12px;border-radius:var(--radius-md);border:1px solid var(--gray-200);">
                    <small style="color:var(--gray-400);">How long resolved IP locations are cached.</small>
                </div>

                <div>
                    <label class="form-label" style="font-weight:600;font-size:.85rem;color:var(--gray-700);display:block;margin-bottom:6px;">
                        Fallback State
                    </label>
                    <select name="geolocation_fallback_state" class="form-select" style="width:100%;padding:8px 12px;border-radius:var(--radius-md);border:1px solid var(--gray-200);">
                        @foreach($stateOptions as $state)
                            <option value="{{ $state->code }}" {{ ($settings['geolocation_fallback_state'] ?? 'FC') === $state->code ? 'selected' : '' }}>
                                {{ $state->location_name }} ({{ $state->code }})
                            </option>
                        @endforeach
                    </select>
                    <small style="color:var(--gray-400);">State used for private/local IP addresses during development.</small>
                </div>

                <div>
                    <label class="form-label" style="font-weight:600;font-size:.85rem;color:var(--gray-700);display:block;margin-bottom:6px;">
                        API Token (optional)
                    </label>
                    <input type="text" name="geolocation_api_token" value="{{ $settings['geolocation_api_token'] ?? '' }}" class="form-control" style="width:100%;padding:8px 12px;border-radius:var(--radius-md);border:1px solid var(--gray-200);" placeholder="Only required for ipapi.co">
                    <small style="color:var(--gray-400);">Optional token for paid geolocation providers.</small>
                </div>
            </div>

            @if($errors->any())
                <div style="padding:12px 16px;border-radius:var(--radius-md);background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;">
                    <ul style="margin:0;padding-left:18px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div style="display:flex;justify-content:flex-end;gap:10px;">
                <a href="{{ route('admin.dashboard') }}" class="btn-nis btn-ghost">Cancel</a>
                <button type="submit" class="btn-nis btn-primary-nis">
                    <i class="fas fa-save"></i> Save Settings
                </button>
            </div>
        </div>
    </form>
</main>

@include('partials.footer3')
