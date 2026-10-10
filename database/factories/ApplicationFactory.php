<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'status' => 'pending',
            'workflow_stage' => 'submitted',
            'category' => 'state',
            'scope_code' => 'LA',
            'return_data' => [],
        ];
    }
}
