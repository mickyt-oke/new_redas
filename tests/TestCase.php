<?php

namespace Tests;

use App\Models\Application;
use App\Models\User;
use App\Services\HashidService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\URL;

abstract class TestCase extends BaseTestCase
{
    /**
     * Build a regular route URL for a hashed application route.
     */
    protected function applicationRoute(string $name, Application $application, array $extra = []): string
    {
        return route($name, array_merge(['applicationHash' => HashidService::encode($application->id)], $extra));
    }

    /**
     * Build a signed route URL for a hashed application route.
     */
    protected function signedApplicationRoute(string $name, Application $application, array $extra = []): string
    {
        return URL::signedRoute($name, array_merge(['applicationHash' => HashidService::encode($application->id)], $extra));
    }

    /**
     * Build a regular route URL for a hashed user route.
     */
    protected function userRoute(string $name, User $user, array $extra = []): string
    {
        return route($name, array_merge(['userHash' => HashidService::encode($user->id)], $extra));
    }

    /**
     * Build a signed route URL for a hashed user route.
     */
    protected function signedUserRoute(string $name, User $user, array $extra = []): string
    {
        return URL::signedRoute($name, array_merge(['userHash' => HashidService::encode($user->id)], $extra));
    }
}
