<?php

// NoteFactory.php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class NoteFactory extends Factory
{
    public function definition(): array
    {
        $noteableType = $this->faker->randomElement([\App\Models\Donation::class, \App\Models\Pledge::class, \App\Models\Individual::class]); // Add more as needed
        $noteable = $noteableType::factory()->create();

        return [
            'noteable_id' => $noteable->id,
            'noteable_type' => $noteableType,
            'body' => $this->faker->paragraph,
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
