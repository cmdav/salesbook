<?php

namespace Database\Factories;

use App\Models\PaymentDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentDetailFactory extends Factory
{
    protected $model = PaymentDetail::class;

    public function definition()
    {
        return [
            'payment_method_id' => $this->faker->uuid(),
            'account_name' => "",
            'account_number' => "",
            'payment_identifier' => 'payment name',
            'created_by' => $this->faker->uuid(),
            'updated_by' => $this->faker->uuid(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
