<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAccessMismatch
{
    /**
     * Redirect authenticated users away from routes that are not intended for
     * their user_category. This runs before the deny-by-default access
     * middleware so users get a friendly redirect instead of a 403.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return $next($request);
        }

        $allowedCategories = $this->allowedCategoriesForRoute($request->route());

        if ($allowedCategories === null) {
            return $next($request);
        }

        if (in_array($user->user_category, $allowedCategories, true)) {
            return $next($request);
        }

        return redirect($user->homeRoute())
            ->with('error', 'This page is not available for your account type.');
    }

    /**
     * Extract the allowed user_category values from the route's access middleware.
     *
     * @return list<string>|null Null when the route has no access middleware.
     */
    private function allowedCategoriesForRoute(?Route $route): ?array
    {
        if ($route === null) {
            return null;
        }

        $middleware = $route->getAction('middleware') ?? [];
        $categories = [];
        $hasAccessMiddleware = false;

        foreach ((array) $middleware as $entry) {
            if (! is_string($entry) || ! str_starts_with($entry, 'access:')) {
                continue;
            }

            $hasAccessMiddleware = true;
            $parameters = substr($entry, strlen('access:'));

            foreach (explode(',', $parameters) as $segment) {
                $segment = trim($segment);

                if (! str_starts_with($segment, 'category=')) {
                    continue;
                }

                $value = substr($segment, strlen('category='));

                foreach (explode('|', $value) as $category) {
                    $category = trim($category);

                    if ($category !== '') {
                        $categories[] = $category;
                    }
                }
            }
        }

        if (! $hasAccessMiddleware) {
            return null;
        }

        return array_values(array_unique($categories));
    }
}
