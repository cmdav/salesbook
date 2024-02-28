<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductSubCategory>
 */
class ProductSubCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sub_category_name' => $this->faker->words(3, true),
            'category_id'=>function () {
                return \App\Models\ProductCategory::first()->id;   
            },
            'created_by'=>'admin'
        ];
    }
}
