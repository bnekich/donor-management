<?php

// FundingRequestFactory.php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FundingRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'recipient_id' => \App\Models\Organization::factory(),
            'amount' => $this->faker->randomFloat(2, 1000, 100000),
            'request_date' => $this->faker->dateTimeThisYear,
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected', 'funded']),
            'approved_amount' => $this->faker->randomFloat(2, 1000, 100000),
            'funded_date' => $this->faker->dateTimeThisYear('+1 month'),
            'notes' => $this->faker->paragraph,
        ];
    }
}
