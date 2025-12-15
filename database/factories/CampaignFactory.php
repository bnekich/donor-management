<?php

// CampaignFactory.php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CampaignFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->catchPhrase,
            'description' => $this->faker->paragraph,
            'start_date' => $this->faker->dateTimeThisYear,
            'end_date' => $this->faker->dateTimeThisYear('+3 months'),
            'goal' => $this->faker->randomFloat(2, 1000, 100000),
            'chapter_id' => 1,
        ];
    }
}
