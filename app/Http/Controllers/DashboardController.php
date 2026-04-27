<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        // Fetch recent applications for the dashboard
        $recentApplications = Application::where('officer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('user.dashboard', compact('recentApplications'));
    }

    public function notifications(): View
    {
        $user = Auth::user();

        // Fetch notifications for the user (placeholder logic)
        $notifications = [
            ['message' => 'Your return has been approved.', 'type' => 'success'],
            ['message' => 'New submission received from John Doe.', 'type' => 'info'],
            ['message' => 'Your profile has been updated successfully.', 'type' => 'success'],
        ];

        return view('user.notifications', compact('notifications'));
    }

    public function submissions(): View
    {
        $user = Auth::user();

        // Fetch all submissions for the user (placeholder logic)
        $submissions = Application::where('officer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.submissions', compact('submissions'));
    }
}