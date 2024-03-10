<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Currency>
 */
class CurrencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'currency_name' => $this->faker->word,
            'currency_symbol' => $this->faker->currencyCode,
            'created_by'=>'admin',
            'updated_by'=>'admin',
        ];
    }
}
