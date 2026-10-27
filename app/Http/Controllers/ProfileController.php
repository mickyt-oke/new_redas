<?php

namespace App\Http\Controllers;

use App\Services\AuditLogger;
use App\Services\PasswordRule;
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
            $rules['password'] = PasswordRule::rules($user, required: (bool) $user->must_change_password, confirmed: true);
        } elseif ($section === 'passcode') {
            $rules['lockscreen_passcode'] = $this->passcodeRules();
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
            $history = $user->password_history ?? [];
            $history[] = $user->password;
            $user->password_history = array_slice($history, -5);
            $user->password = Hash::make($validated['password']);
            $user->password_changed_at = now();
            $user->must_change_password = false;
            $messages[] = 'password';

            AuditLogger::logCrud($user, 'security.password_changed', 'user', $user->id);
        }

        if ($section === 'passcode') {
            if (! empty($validated['remove_lockscreen_passcode'])) {
                $user->lockscreen_passcode = null;
                $messages[] = 'lockscreen passcode removed';

                AuditLogger::logCrud($user, 'security.lockscreen_passcode_removed', 'user', $user->id);
            } elseif (! empty($validated['lockscreen_passcode'])) {
                $user->lockscreen_passcode = Hash::make($validated['lockscreen_passcode']);
                $messages[] = 'lockscreen passcode';

                AuditLogger::logCrud($user, 'security.lockscreen_passcode_set', 'user', $user->id);
            }
        }

        $user->save();

        $status = $messages
            ? 'Updated: ' . implode(', ', $messages) . '.'
            : 'No changes were made.';

        return redirect()->route('user.profile')->with('status', $status);
    }

    /**
     * Validation rules for the lockscreen passcode.
     *
     * @return list<string|\Closure>
     */
    private function passcodeRules(): array
    {
        return [
            'nullable',
            'string',
            'min:6',
            'max:20',
            'confirmed',
            function (string $attribute, mixed $value, \Closure $fail): void {
                if (! is_string($value)) {
                    return;
                }

                $weak = ['0000', '1234', '121212'];

                if (in_array($value, $weak, true)) {
                    $fail('This passcode is too easy to guess.');
                    return;
                }

                if (preg_match('/^(\d)\1+$/', $value)) {
                    $fail('Repeated digits are not allowed.');
                    return;
                }

                if (preg_match('/^\d+$/', $value) && strlen($value) < 8) {
                    $fail('Numeric passcodes must be at least 8 digits.');
                }
            },
        ];
    }
}
