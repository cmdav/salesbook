<?php

namespace Database\Factories;

use App\Models\BusinessBranch;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessBranchFactory extends Factory
{
    //protected $model = BusinessBranch::class;

    public function definition()
    {
        return [
           
            'name' => $this->faker->company,
            'state_id' => 306,
            'postal_code' => $this->faker->postcode,
            'city' => $this->faker->city,
            'country_id' => 161,
            'contact_person' => $this->faker->name,
            'phone_number' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
        ];
    }
}