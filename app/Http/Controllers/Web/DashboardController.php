<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Metadata for directorate return views. Fields are now defined explicitly in
     * resources/views/user/directorates/{slug}.blade.php for easier reference and design.
     */
    private const DIRECTORATES = [
        'hrm' => [
            'name' => 'Human Resources Management (HRM)',
            'icon' => 'fas fa-users',
        ],
        'prs' => [
            'name' => 'Planning, Research and Statistics (PRS)',
            'icon' => 'fas fa-chart-bar',
        ],
        'finance' => [
            'name' => 'Finance and Accounts',
            'icon' => 'fas fa-coins',
        ],
        'investigation' => [
            'name' => 'Investigation and Compliance',
            'icon' => 'fas fa-search',
        ],
        'passport' => [
            'name' => 'Passport and Other Travel Documents',
            'icon' => 'fas fa-passport',
        ],
        'visa' => [
            'name' => 'Visa and Residency',
            'icon' => 'fas fa-stamp',
        ],
        'migration' => [
            'name' => 'Migration Directorate',
            'icon' => 'fas fa-globe-africa',
        ],
        'border' => [
            'name' => 'Border Management',
            'icon' => 'fas fa-border-all',
        ],
        'ict' => [
            'name' => 'ICT Directorate',
            'icon' => 'fas fa-laptop-code',
        ],
        'works-logistics' => [
            'name' => 'Works and Logistics',
            'icon' => 'fas fa-truck',
        ],
    ];

    public function showDirectorate(string $slug): View
    {
        $directorate = self::DIRECTORATES[$slug] ?? null;
        abort_if($directorate === null, 404);

        return view('user.directorates.' . $slug, [
            'slug' => $slug,
            'directorateName' => $directorate['name'],
            'directorateIcon' => $directorate['icon'],
            'allDirectorates' => self::DIRECTORATES,
        ]);
    }

    public function storeDirectorate(Request $request, string $slug): RedirectResponse
    {
        $directorate = self::DIRECTORATES[$slug] ?? null;
        abort_if($directorate === null, 404);

        $request->validate([
            'report_period' => ['required', 'date_format:Y-m'],
            'reporting_officer' => ['required', 'string', 'max:120'],
            'data_consent' => ['required', 'accepted'],
        ]);

        return redirect()
            ->route('user.directorates.show', $slug)
            ->with('status', $directorate['name'] . ' return submitted successfully.');
    }
}
