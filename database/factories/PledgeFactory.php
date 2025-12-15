<?php

// PledgeFactory.php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PledgeFactory extends Factory
{
    public function definition(): array
    {
        $donorType = $this->faker->randomElement([\App\Models\Individual::class, \App\Models\Organization::class]);
        $donor = $donorType::factory()->create();

        return [
            'donor_id' => $donor->id,
            'donor_type' => $donorType,
            'amount' => $this->faker->randomFloat(2, 50, 5000),
            'pledge_date' => $this->faker->dateTimeThisYear,
            'due_date' => $this->faker->dateTimeThisYear('+1 year'),
            'status' => $this->faker->randomElement(['pending', 'fulfilled', 'cancelled']),
            'campaign_id' => fake()->numberBetween(1, 9),
            'chapter_id' => fake()->numberBetween(1, 9),
            'account_id' => fake()->numberBetween(1, 9),
            'notes' => $this->faker->paragraph,
        ];
    }
}
