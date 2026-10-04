<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LockscreenController extends Controller
{
    public function show()
    {
        return view('auth.lockscreen');
    }

    public function unlock(Request $request)
    {
        $request->validate([
            'passcode' => 'required|string',
        ]);

        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->lockscreen_passcode) {
            // If no lockscreen passcode is set, fallback to checking the main account password
            if (Hash::check($request->input('passcode'), $user->password)) {
                $request->session()->forget('session.locked');
                return redirect()->intended('/dashboard');
            }
        } elseif (Hash::check($request->input('passcode'), $user->lockscreen_passcode)) {
            $request->session()->forget('session.locked');
            return redirect()->intended('/dashboard');
        }

        throw ValidationException::withMessages([
            'passcode' => ['The provided passcode is incorrect.'],
        ]);
    }

    public function lock(Request $request)
    {
        $request->session()->put('session.locked', true);
        return response()->json(['status' => 'locked']);
    }
}
