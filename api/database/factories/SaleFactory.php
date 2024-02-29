<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'store_id' =>function () {
                return \App\Models\Store::first()->id;   
            },
            'organization_id' =>function () {
                return \App\Models\Organization::first()->id;   
            },
            'customer_id' =>function () {
                return \App\Models\User:: where("type_id", 0)->first()->id;   
            },
            'price' => $this->faker->numberBetween(100, 5000), 
            'quantity' => $this->faker->numberBetween(1, 100), 
            'sales_owner'=>function () {
                return \App\Models\User:: where("type_id", 1)->first()->id;    
            },
            'created_by' => $this->faker->uuid(),
        ];
    }
}
