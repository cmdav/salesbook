<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SupplierProduct>
 */
class SupplierProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
           
            'product_name' => $this->faker->words(3, true),
            'product_description' => $this->faker->sentence(),
            'product_image' => $this->faker->imageUrl(640, 480, 'products', true),
            'product_id' =>function () {
                return \App\Models\Product::first()->id;   
            },
            'supplier_id' =>function () {
                return \App\Models\Supplier::first()->id;   
            },
            'created_by'=>'admin'
        ];
    }
}
