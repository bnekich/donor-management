<?php

// ChapterFactory.php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ChapterFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->city . ' Chapter',
            'location' => $this->faker->city . ', ' . $this->faker->stateAbbr,
            'description' => $this->faker->paragraph,
        ];
    }
}
