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
        $supplierEmail = 'supplier@gmail.com';
    
        // Get the user ID for the supplier
        $supplierUserId = \App\Models\User::where('email', $supplierEmail)->first()->id ?? null;
        
        // Now, get a store ID that belongs to this supplier
        //$store = \App\Models\Store::where('store_owner', $supplierUserId)->inRandomOrder()->first();
        $store = \App\Models\Store::first()->id;
        
        return [
            'store_id' =>function () {
                return \App\Models\Store::first()->id;   
            },
            'customer_id' => function () {
                return \App\Models\User::where("type_id", 0)->first()->id;   
            },
            'price_sold_at' => $this->faker->numberBetween(100, 5000), 
            'quantity' => 3, 
            'sales_owner' => function () use ($supplierUserId) {
                return $supplierUserId;    
            },
            'created_by'=>'admin',
            'updated_by'=>'admin',
        ];
    }
    
}
