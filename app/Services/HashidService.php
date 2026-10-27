<?php

namespace App\Services;

use Hashids\Hashids;
use Illuminate\Support\Facades\Config;

final class HashidService
{
    private static ?Hashids $hashids = null;

    private static function hashids(): Hashids
    {
        if (self::$hashids === null) {
            $salt = (string) config('app.hashid_salt');

            if ($salt === '') {
                throw new \RuntimeException('HASHID_SALT is not configured.');
            }

            $minHashLength = 8;

            self::$hashids = new Hashids($salt, $minHashLength);
        }

        return self::$hashids;
    }

    /**
     * Encode an integer ID to a hash string.
     */
    public static function encode(int $id): string
    {
        return self::hashids()->encode($id);
    }

    /**
     * Decode a hash string back to an integer ID.
     */
    public static function decode(string $hash): ?int
    {
        $decoded = self::hashids()->decode($hash);

        if (empty($decoded)) {
            return null;
        }

        return $decoded[0];
    }
}
