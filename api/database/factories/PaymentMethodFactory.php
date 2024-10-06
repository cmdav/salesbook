<?php

namespace Database\Factories;

use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentMethodFactory extends Factory
{
    protected $model = PaymentMethod::class;

    public function definition()
    {
        return [
            'id' => (string) Str::uuid(), // Generate a UUID for each payment method
            'payment_name' => $this->faker->unique()->word(), // Generate a unique payment name
        ];
    }
}
