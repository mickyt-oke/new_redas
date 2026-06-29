<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;


class AdminDashController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }
}

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }
}


