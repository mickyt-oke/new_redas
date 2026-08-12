<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * This Controller handles the dashboard functions for state users (officers) only. It provides methods to display the dashboard, notifications, and submissions for the authenticated user.
     */
    public function notifications(): View
    {
        $notifications = [
            ['message' => 'Your return has been approved.', 'type' => 'success'],
            ['message' => 'New submission received from John Doe.', 'type' => 'info'],
            ['message' => 'Your profile has been updated successfully.', 'type' => 'success'],
        ];

        return view('user.states.notifications', compact('notifications'));
    }

    /**
     * Display user submissions.
     */
    public function submissions(): View
    {
        $user = Auth::user();

        $submissions = Application::query()->where(['officer_id' => $user->id])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.states.submissions', compact('submissions'));
    }

    public function dashboard(): View
    {
        $user = Auth::user();

        $submissions = Application::query()->where(['officer_id' => $user->id])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.states.dashboard', compact('submissions'));
    }
}
