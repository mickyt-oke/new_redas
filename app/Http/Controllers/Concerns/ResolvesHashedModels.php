<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Application;
use App\Models\User;
use App\Services\HashidService;

/**
 * Helpers for controllers that receive obfuscated hashids instead of numeric IDs.
 */
trait ResolvesHashedModels
{
    /**
     * Decode an application hash and return the model, or abort 404.
     */
    private function resolveApplication(string $hash): Application
    {
        $id = HashidService::decode($hash);

        if ($id === null) {
            abort(404, 'Invalid return identifier.');
        }

        $application = Application::find($id);

        if ($application === null) {
            abort(404, 'Return not found.');
        }

        return $application;
    }

    /**
     * Decode a user hash and return the model, or abort 404.
     */
    private function resolveUser(string $hash): User
    {
        $id = HashidService::decode($hash);

        if ($id === null) {
            abort(404, 'Invalid user identifier.');
        }

        $user = User::find($id);

        if ($user === null) {
            abort(404, 'User not found.');
        }

        return $user;
    }
}
