<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display user notifications.
     */
    public function notifications(): View
    {
        $notifications = [
            ['message' => 'Your return has been approved.', 'type' => 'success'],
            ['message' => 'New submission received from John Doe.', 'type' => 'info'],
            ['message' => 'Your profile has been updated successfully.', 'type' => 'success'],
        ];

        return view('user.notifications', compact('notifications'));
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

        return view('user.submissions', compact('submissions'));
    }
}