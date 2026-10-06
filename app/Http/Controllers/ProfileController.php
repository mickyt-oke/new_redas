<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('user.profile', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'lockscreen_passcode' => ['nullable', 'string', 'max:20'],
        ]);

        $user = Auth::user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $user->password = Hash::make($validated['password']);
        $user->must_change_password = false;

        if ($request->filled('lockscreen_passcode')) {
            $user->lockscreen_passcode = Hash::make($validated['lockscreen_passcode']);
        } elseif ($request->has('lockscreen_passcode') && empty($request->lockscreen_passcode)) {
            $user->lockscreen_passcode = null;
        }

        $user->save();

        return redirect()->route('user.profile')->with('status', 'Profile updated successfully.');
    }
}
