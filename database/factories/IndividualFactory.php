<?php

// IndividualFactory.php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class IndividualFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'address_line1' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => $this->faker->stateAbbr,
            'zip' => $this->faker->postcode,
            'birthday' => $this->faker->date('Y-m-d', 'now'),
            'occupation' => $this->faker->jobTitle,
            'organization_id' => \App\Models\Organization::factory(),
        ];
    }
}
