<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


class TaskFactory extends Factory
{
   
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'description' => fake()->text(),
            'start_time' => now(),
            'end_time' => now(),
            'user_id' => \App\Models\User::factory(),
        
        ];
    }

   
}
