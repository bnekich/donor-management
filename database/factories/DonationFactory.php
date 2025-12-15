<?php

// DonationFactory.php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DonationFactory extends Factory
{
    public function definition(): array
    {
        $donorType = $this->faker->randomElement([\App\Models\Individual::class, \App\Models\Organization::class]);
        $donor = $donorType::factory()->create();

        return [
            'donor_id' => $donor->id,
            'donor_type' => $donorType,
            'amount' => $this->faker->randomFloat(2, 50, 5000),
            'date' => $this->faker->dateTimeThisYear,
            'payment_method' => $this->faker->randomElement(['credit_card', 'check', 'direct', 'paypal']),
            'transaction_id' => $this->faker->uuid,
            'pledge_id' => fake()->numberBetween(1, 9),
            'campaign_id' => fake()->numberBetween(1, 9),
            'chapter_id' => fake()->numberBetween(1, 9),
            'account_id' => fake()->numberBetween(1, 9),
            'notes' => $this->faker->paragraph,
        ];
    }
}
