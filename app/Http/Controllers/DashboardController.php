<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $applications = Application::where('user_id', auth()->id())->get();
        return view('dashboard', compact('applications'));
    }
}