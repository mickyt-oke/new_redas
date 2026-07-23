<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccess
{
    /**
     * Enforce dynamic access using deny-by-default rules.
     *
     * Usage examples:
     *   middleware('access:category=state_user|desk_admin,location=state,role=user|minLevel=0')
     */
    public function handle(Request $request, Closure $next, string ...$constraints): Response
    {
        $user = $request->user();

        if (!$user instanceof User) {
            abort(403, 'Unauthorized.');
        }

        $parsed = $this->parseConstraints($constraints);

        if (!$this->passesAccessChecks($user, $parsed)) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }

    /**
     * @param array<int, string> $constraints
     * @return array{
     *   category: array<int, string>,
     *   location: array<int, string>,
     *   role: array<int, string>,
     *   minLevel: int|null
     * }
     */
    private function parseConstraints(array $constraints): array
    {
        $parsed = [
            'category' => [],
            'location' => [],
            'role' => [],
            'minLevel' => null,
        ];

        foreach ($constraints as $constraint) {
            $segments = array_values(array_filter(array_map(
                static fn (string $item): string => trim($item),
                explode(',', $constraint)
            )));

            foreach ($segments as $segment) {
                $parts = explode('=', $segment, 2);
                if (count($parts) !== 2) {
                    continue;
                }

                $key = trim($parts[0]);
                $value = trim($parts[1]);

                if ($key === '' || $value === '') {
                    continue;
                }

                if (str_contains($value, 'minLevel=')) {
                    [$value, $minLevelValue] = explode('minLevel=', $value, 2);
                    $value = trim($value);

                    if (is_numeric($minLevelValue)) {
                        $parsed['minLevel'] = (int) $minLevelValue;
                    }
                }

                if ($key === 'minLevel') {
                    if (is_numeric($value)) {
                        $parsed['minLevel'] = (int) $value;
                    }
                    continue;
                }

                $values = array_values(array_filter(array_map(
                    static fn (string $item): string => trim($item),
                    explode('|', $value)
                )));

                if (in_array($key, ['category', 'location', 'role'], true)) {
                    $parsed[$key] = $values;
                }
            }
        }

        return $parsed;
    }

    /**
     * @param array{
     *   category: array<int, string>,
     *   location: array<int, string>,
     *   role: array<int, string>,
     *   minLevel: int|null
     * } $parsed
     */
    private function passesAccessChecks(User $user, array $parsed): bool
    {
        // Category check
        if (!empty($parsed['category'])) {
            foreach ($parsed['category'] as $category) {
                if (!in_array($category, User::ACCESS_CATEGORIES, true)) {
                    return false; // strict validation
                }
            }

            if (!$user->hasCategory(...$parsed['category'])) {
                return false;
            }
        }

        // Location check
        if (!empty($parsed['location'])) {
            foreach ($parsed['location'] as $location) {
                if (!in_array($location, User::LOCATION_TYPES, true)) {
                    return false; // strict validation
                }
            }

            if (!$user->hasLocationType(...$parsed['location'])) {
                return false;
            }
        }

        // Role type check (new semantic role layer)
        if (!empty($parsed['role'])) {
            foreach ($parsed['role'] as $role) {
                if (!in_array($role, User::ROLE_TYPES, true) && !in_array($role, ['admin', 'zonal', 'state', 'officer', 'directorate', 'user'], true)) {
                    return false; // strict validation
                }
            }

            $legacyRoleAliases = [
                'user' => 'officer',
                'admin' => 'admin',
                'state' => 'admin',
                'zonal' => 'admin',
                'directorate' => 'user',
                'super_admin' => 'admin',
            ];

            $expandedLegacyRoles = $parsed['role'];
            foreach ($parsed['role'] as $role) {
                if (array_key_exists($role, $legacyRoleAliases)) {
                    $expandedLegacyRoles[] = $legacyRoleAliases[$role];
                }
            }

            $expandedLegacyRoles = array_values(array_unique($expandedLegacyRoles));

            $matched = $user->hasRoleType(...$parsed['role']) || $user->hasLegacyRole(...$expandedLegacyRoles);

            if (!$matched) {
                return false;
            }
        }

        // Access level check
        if ($parsed['minLevel'] !== null && !$user->hasMinimumAccessLevel($parsed['minLevel'])) {
            return false;
        }

        return true;
    }
}
