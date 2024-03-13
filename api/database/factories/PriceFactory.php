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

            'product_type_id'  =>function () {
               
               return \App\Models\ProductType::first()->id;   
            },
            'supplier_id'  =>function () {
                return \App\Models\User::where('email', 'supplier@gmail.com')->first()->id;   
            },
            'cost_price' => $this->faker->randomNumber(4),
            'selling_price' => $this->faker->randomNumber(4),
            'system_price' => $this->faker->randomNumber(4),
            'currency_id'  =>function () {
                return \App\Models\Currency::first()->id;   
            },
            'discount' => $this->faker->numberBetween(0, 100),
            'organization_id' => $this->faker->uuid,
            'created_by' => $this->faker->uuid,
            'updated_by' => $this->faker->uuid
        ];
    }
}
