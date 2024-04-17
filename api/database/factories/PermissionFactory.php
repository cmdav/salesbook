<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Currency>
 */
class PermissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
			'read' => $this->faker->boolean,
            'write' => $this->faker->boolean,
            'update' => $this->faker->boolean,
            'del' => $this->faker->boolean,
            'created_by'=>'admin',
            'updated_by'=>'admin',
        ];
    }
}
