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
            'supplier_product_id'  =>function () {
                return \App\Models\SupplierProduct::first()->id;   
            },
            'currency'  =>function () {
                return \App\Models\Currency::first()->id;   
            },
            'discount' => $this->faker->numberBetween(0, 50), 
            'batch_no' => $this->faker->bothify('Batch-####-???'), 
            'product_identifier' => $this->faker->unique()->bothify('Prod-####-???'), 
            'supplier_price' => $this->faker->numberBetween(100, 1000), 
            'expired_date' => "2022-10-02", 
            'quantity' => 50,
            'store_owner' => function () {
                
                return \App\Models\User::where('email','admin@gmail.com')->first()->id;
            },
            'created_by' => 'admin' 
        ];
    }
}
