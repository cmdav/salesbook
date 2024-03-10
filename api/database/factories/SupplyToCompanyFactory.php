<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class SupplyToCompanyFactory extends Factory
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
    
            'organization_id'  =>function () {
                

               return \App\Models\Organization::inRandomOrder()->first()->id;    
            },
            'supplier_id'  =>function () {
                return \App\Models\User::where('email','supplier@gmail.com')->first()->id;   
            },
            'created_by'=>'admin',
            'updated_by'=>'admin',
        ];
    }
}
