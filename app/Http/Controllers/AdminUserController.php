<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->primary_location_code === 'VISA' || $user->primary_location_code === 'ICT') {
            $users = User::where('primary_location_code', $user->primary_location_code)
                ->orderBy('id', 'desc')
                ->get();
        } else {
            $users = User::orderBy('id', 'desc')->get();
        }
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'service_number' => 'required|digits:5|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:officer,admin',
        ]);

        $creator = auth()->user();
        
        $validated['primary_location_type'] = $creator->primary_location_type ?: 'directorate';
        $validated['primary_location_code'] = $creator->primary_location_code ?: 'VISA';
        
        if ($validated['role'] === 'admin') {
            $validated['user_category'] = 'directorate_admin';
            $validated['access_level'] = 5;
        } else {
            $validated['user_category'] = 'directorate_user';
            $validated['access_level'] = 3;
        }

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()
            ->route('admin.users')
            ->with('status', 'User created successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users')
                ->with('error', 'You cannot delete yourself.');
        }
        $user->delete();

        return redirect()
            ->route('admin.users')
            ->with('status', 'User removed successfully.');
    }
}
