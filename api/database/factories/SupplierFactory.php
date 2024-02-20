<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' =>function () {
                return \App\Models\User::where('type_id', 1)->inRandomOrder()->first();   
            },
            'bank_name' => $this->faker->company,
            'account_number' => $this->faker->randomNumber(8),
            'account_name' => $this->faker->name,
            'state' => $this->faker->state,
            'address' => $this->faker->address,
            'dob' => $this->faker->dateTimeBetween('-70 years', '-18 years')->format('Y-m-d'),
        ];
    }
}
