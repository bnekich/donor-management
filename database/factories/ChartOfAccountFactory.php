<?php

// ChartOfAccountFactory.php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ChartOfAccountFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->numerify('###-###'),
            'name' => $this->faker->word . ' Account',
            'description' => $this->faker->sentence,
            'type' => $this->faker->randomElement(['income', 'expense', 'asset', 'liability', 'equity']),
        ];
    }
}
