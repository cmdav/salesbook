<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
public function definition(): array 
    {
        return [
            'supplier_id'  =>function () {
                return \App\Models\User:: where('email','supplier@gmail.com')->first()->id;   
            },
            'product_type_id' =>function () {
                return \App\Models\Product::first()->id;   
            },

            'price_id' =>function () {
                return \App\Models\Price::first()->id;   
            },
            'currency_id'  =>function () {
                return \App\Models\Currency::first()->id;   
            },
            'discount' => $this->faker->numberBetween(0, 50), 
            'batch_no' => $this->faker->bothify('Batch-####-???'), 
            'product_identifier' => $this->faker->unique()->bothify('Prod-####-???'), 
            'selling_price' => $this->faker->numberBetween(100, 1000), 
            'expired_date' => "2022-10-02", 
            'quantity' => 50,
            'purchase_owner' => function () {
                
                return \App\Models\User::where('email','admin@gmail.com')->first()->id;
            },
            'created_by'=>'admin',
            'updated_by'=>'admin', 
        ];
    }
}
