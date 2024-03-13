<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
public function definition(): array
{
    return [
        'product_type_id' =>function () {
            return \App\Models\ProductType::first()->id;   
        },
        'price_id' =>function () {
            
            return \App\Models\Price::first()->id;
        },
        'quantity_available' => 50,
        'store_owner' => function () {
            
            return \App\Models\User::where('email','admin@gmail.com')->first()->id;
        },
        'created_by'=>'admin',
        'updated_by'=>'admin',
    ];
}
}
