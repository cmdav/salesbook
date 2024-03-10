<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SupplierOrganization>
 */
class SupplierOrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        
            
            return [

                'supplier_id' => function () {
                    return \App\Models\User::where('type_id', 1)->inRandomOrder()->first();   
                },
                'organization_id' =>function () {
                    return \App\Models\Organization::first()->id;   
                },
                'created_by'=>'admin',
                'updated_by'=>'admin',
            ];
        
    }
}
