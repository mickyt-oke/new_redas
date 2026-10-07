<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('user.profile', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $section = $request->input('section');

        $rules = [
            'current_password' => ['required', 'string'],
        ];

        if ($section === 'password') {
            $rules['password'] = [
                Rule::requiredIf($user->must_change_password),
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ];
        } elseif ($section === 'passcode') {
            $rules['lockscreen_passcode'] = ['nullable', 'string', 'min:4', 'max:20', 'confirmed'];
            $rules['remove_lockscreen_passcode'] = ['nullable', 'boolean'];
        }

        $validated = $request->validate($rules);

        if (! Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $messages = [];

        if ($section === 'password' && ! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
            $user->must_change_password = false;
            $messages[] = 'password';
        }

        if ($section === 'passcode') {
            if (! empty($validated['remove_lockscreen_passcode'])) {
                $user->lockscreen_passcode = null;
                $messages[] = 'lockscreen passcode removed';
            } elseif (! empty($validated['lockscreen_passcode'])) {
                $user->lockscreen_passcode = Hash::make($validated['lockscreen_passcode']);
                $messages[] = 'lockscreen passcode';
            }
        }

        $user->save();

        $status = $messages
            ? 'Updated: ' . implode(', ', $messages) . '.'
            : 'No changes were made.';

        return redirect()->route('user.profile')->with('status', $status);
    }
}
