<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->name(),
            'company_name' => $this->faker->name(),
            'contact_person' => $this->faker->name(),
            'middle_name' => $this->faker->name(),
            'last_name' => $this->faker->name(),
            'phone_number' => $this->faker->numberBetween(21012345670, 81012345690),
            'type_id' =>$this->faker->numberBetween(0, 2),
            'organization_id' =>function () {
                return \App\Models\Organization::first()->id;   
            },
            'token'=> Str::uuid(),
            'role_id'=>0,
            'dob' => $this->faker->date(),
            'email' =>$this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
