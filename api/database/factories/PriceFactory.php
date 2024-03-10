<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\product>
 */
class PriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            'product_type_id' => $this->faker->uuid,
            'supplier_id' => $this->faker->uuid,
            'product_price' => $this->faker->randomNumber(4),
            'product_currency' => $this->faker->uuid,
            'discount' => $this->faker->numberBetween(0, 100),
            'organization_id' => $this->faker->uuid,
            'created_by' => $this->faker->uuid,
            'updated_by' => $this->faker->uuid
        ];
    }
}
