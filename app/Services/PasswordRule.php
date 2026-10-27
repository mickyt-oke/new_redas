<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordRule
{
    /**
     * Build the standard application password validation rules.
     *
     * @return list<string|\Illuminate\Validation\Rules\Password|\Closure>
     */
    public static function rules(?User $user = null, bool $required = true, bool $confirmed = true): array
    {
        $rules = [
            $required ? 'required' : 'nullable',
            'string',
            Password::min(12)->uncompromised()->letters()->numbers(),
        ];

        if ($confirmed) {
            $rules[] = 'confirmed';
        }

        $rules[] = static::personalInfoRule($user);
        $rules[] = static::historyRule($user);

        return $rules;
    }

    /**
     * Reject passwords that contain the user's name, email local-part or service number.
     */
    private static function personalInfoRule(?User $user): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) use ($user): void {
            if (! $user instanceof User || ! is_string($value)) {
                return;
            }

            $lowerValue = strtolower($value);

            $forbidden = array_filter([
                $user->name,
                explode('@', (string) $user->email)[0] ?? null,
                $user->service_number,
                $user->service_number ? str_replace('/', '', (string) $user->service_number) : null,
            ]);

            foreach ($forbidden as $fragment) {
                $fragment = (string) $fragment;
                if ($fragment === '' || strlen($fragment) < 3) {
                    continue;
                }

                if (str_contains($lowerValue, strtolower($fragment))) {
                    $fail('The password must not contain your name, email or service number.');
                    return;
                }
            }
        };
    }

    /**
     * Reject reuse of any of the last five stored password hashes.
     */
    private static function historyRule(?User $user): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) use ($user): void {
            if (! $user instanceof User || ! is_string($value)) {
                return;
            }

            $history = $user->password_history ?? [];

            foreach (array_slice((array) $history, -5) as $oldHash) {
                if (! is_string($oldHash)) {
                    continue;
                }

                if (Hash::check($value, $oldHash)) {
                    $fail('You may not reuse a recent password.');
                    return;
                }
            }
        };
    }
}
