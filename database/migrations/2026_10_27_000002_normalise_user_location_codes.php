<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Normalise existing location and scope codes to trimmed uppercase values.
     */
    public function up(): void
    {
        foreach (User::query()->cursor() as $user) {
            $needsSave = false;

            foreach (['primary_location_code','assigned_state_code','assigned_zonal_command_code','assigned_cgis_unit_code','assigned_directorate_code','geo_state'] as $field) {
                $value = $user->{$field};
                if ($value !== null) {
                    $normalised = trim($value) === '' ? null : strtoupper(trim($value));
                    if ($normalised !== $value) {
                        $user->{$field} = $normalised;
                        $needsSave = true;
                    }
                }
            }

            if ($needsSave) {
                $user->saveQuietly();
            }
        }
    }

    public function down(): void
    {
        // Not reversible.
    }
};
