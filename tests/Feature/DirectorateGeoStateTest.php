<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class DirectorateGeoStateTest extends TestCase
{
    public function test_directorate_users_do_not_require_geo_state_for_access_checks(): void
    {
        $user = new User();
        $user->forceFill([
            'user_category' => 'directorate_user',
            'primary_location_type' => 'directorate',
            'role' => 'directorate',
            'primary_location_code' => 'FC',
            'geo_state' => 'FC',
        ]);

        $this->assertNull($user->requiredGeoState());
        $this->assertFalse($user->isGeoStateEnforced());
    }
}
