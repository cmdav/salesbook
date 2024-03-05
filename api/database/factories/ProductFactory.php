<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\product>
 */
class ProductFactory extends Factory
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
            'measurement_id' =>function () {
                return \App\Models\Measurement::first()->id;   
            },
            'sub_category_id' => function () {
                $subCategory = \App\Models\ProductSubCategory::inRandomOrder()->first() ?? factory(\App\Models\ProductSubCategory::class)->create();
                return $subCategory->id;
            },
            'category_id' => function (array $attributes) {
                // Retrieve the sub_category using the sub_category_id
                $subCategory = \App\Models\ProductSubCategory::find($attributes['sub_category_id']);
                // Return the category_id from the sub_category, if it exists
                return $subCategory ? $subCategory->category_id : factory(\App\Models\ProductCategory::class)->create()->id;
            },
            'created_by'=>'admin'
        ];
    }
}
