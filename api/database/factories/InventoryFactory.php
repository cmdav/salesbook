<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\product>
 */
class InventoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $supplierId = \App\Models\User::where('email', 'supplier@gmail.com')->first()->id;
        
        return [
           
            'supplier_product_id'  =>function () use($supplierId) {
                return \App\Models\SupplierProduct::where('supplier_id', $supplierId)->inRandomOrder()->first()->id ?? null;
            },
          
           
            'store_id' =>function () {
                return \App\Models\Store::first()->id;   
            },
			'quantity_available' => 100,
            'last_updated_by'=>'admin',
            'created_by' => 'admin'
        ];
    }
}
