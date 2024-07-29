<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            'organization_name' => $this->faker->company,
           // 'organization_url' => "http://google.com",
            'organization_code' => 123456,
            'company_email'=>'',
            'organization_logo' => $this->faker->imageUrl(640, 480, 'business'),
            'created_by'=>'admin',
            'updated_by'=>'admin',
            //'company_name'=>$this->faker->company,
            'company_address'=>'No 4, Allen Street',
            'contact_person'=>'Admin account',
            'user_id'=>'admin',
        ];
    }
}
