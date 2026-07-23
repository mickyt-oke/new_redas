<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrimaryLocationCode;
use App\Models\Setting;
use App\Services\AuditLogger;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminSettingController extends Controller
{
    /**
     * Display the system settings form.
     */
    public function index()
    {
        $settings = Setting::all()->keyBy('key');

        $defaults = [
            'abac_enabled' => true,
            'abac_location_enforcement' => true,
            'abac_hq_bypass' => false,
            'abac_allowed_countries' => 'Nigeria',
            'geolocation_provider' => 'ip-api',
            'geolocation_cache_ttl' => 1440,
            'geolocation_fallback_state' => 'FC',
            'geolocation_api_token' => null,
        ];

        $values = [];
        foreach ($defaults as $key => $default) {
            $setting = $settings->get($key);
            $value = $setting?->typedValue() ?? $default;

            if (is_bool($default)) {
                $values[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;
            } elseif (is_int($default)) {
                $values[$key] = is_numeric($value) ? (int) $value : $default;
            } else {
                $values[$key] = $value;
            }
        }

        $stateOptions = PrimaryLocationCode::orderBy('location_name')->get();

        return view('admin.settings.index', [
            'settings' => $values,
            'stateOptions' => $stateOptions,
        ]);
    }

    /**
     * Update system settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'abac_enabled' => 'required|boolean',
            'abac_location_enforcement' => 'required|boolean',
            'abac_hq_bypass' => 'required|boolean',
            'abac_allowed_countries' => 'required|string|max:255',
            'geolocation_provider' => ['required', 'string', Rule::in(['ip-api', 'ipapi'])],
            'geolocation_cache_ttl' => 'required|integer|min:0|max:10080',
            'geolocation_fallback_state' => 'required|string|size:2',
            'geolocation_api_token' => 'nullable|string|max:255',
        ]);

        $user = $request->user();

        $settings = Setting::all()->keyBy('key');
        $types = [
            'abac_enabled' => 'boolean',
            'abac_location_enforcement' => 'boolean',
            'abac_hq_bypass' => 'boolean',
            'abac_allowed_countries' => 'string',
            'geolocation_provider' => 'string',
            'geolocation_cache_ttl' => 'integer',
            'geolocation_fallback_state' => 'string',
            'geolocation_api_token' => 'string',
        ];

        foreach ($validated as $key => $newValue) {
            $oldValue = $settings->get($key)?->typedValue();

            if (is_bool($oldValue) && is_numeric($newValue)) {
                $newValue = (bool) $newValue;
            }

            if ($oldValue !== $newValue) {
                SettingService::set($key, $newValue, $types[$key] ?? 'string');
                AuditLogger::logSettingChange($user, $key, $oldValue, $newValue);
            }
        }

        return redirect()
            ->route('admin.settings.index')
            ->with('status', 'System settings updated successfully.');
    }
}
