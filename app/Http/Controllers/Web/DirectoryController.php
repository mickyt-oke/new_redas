<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\NisDirectory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DirectoryController extends Controller
{
    private const CATEGORIES = [
        'passport' => 'NIS Passport Issuing Centres',
        'state_command' => 'NIS State & Special Commands',
        'zonal' => 'NIS Zonal Commands',
        'border' => 'NIS Land Border Control Posts',
        'airport' => 'NIS International Airport Commands',
        'marine' => 'NIS Marine & Seaport Commands',
        'training' => 'NIS Training Institutions',
        'foreign_mission' => 'Nigerian Foreign Missions',
    ];

    /**
     * Display the public NIS directory with tabs, search and pagination.
     */
    public function index(Request $request): View
    {
        $activeTab = $request->get('tab', 'passport');
        if (! array_key_exists($activeTab, self::CATEGORIES)) {
            $activeTab = 'passport';
        }

        $search = $request->get('search');
        $perPage = 15;

        $items = NisDirectory::category($activeTab)
            ->search($search)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        $counts = NisDirectory::search($search)
            ->selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        foreach (self::CATEGORIES as $key => $label) {
            $counts[$key] = $counts[$key] ?? 0;
        }

        return view('directory', [
            'categories' => self::CATEGORIES,
            'activeTab' => $activeTab,
            'items' => $items,
            'search' => $search,
            'counts' => $counts,
            'icons' => [
                'passport' => 'fa-passport',
                'state_command' => 'fa-building',
                'zonal' => 'fa-layer-group',
                'border' => 'fa-road',
                'airport' => 'fa-plane-departure',
                'marine' => 'fa-ship',
                'training' => 'fa-graduation-cap',
                'foreign_mission' => 'fa-globe',
            ],
        ]);
    }
}
