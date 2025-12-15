<?php

// RelationshipFactory.php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RelationshipFactory extends Factory
{
    public function definition(): array
    {
        $fromType = $this->faker->randomElement([\App\Models\Individual::class, \App\Models\Organization::class]);
        $from = $fromType::factory()->create();
        $toType = $this->faker->randomElement([\App\Models\Individual::class, \App\Models\Organization::class]);
        $to = $toType::factory()->create();

        return [
            'from_type' => $fromType,
            'from_id' => $from->id,
            'to_type' => $toType,
            'to_id' => $to->id,
            'relationship_type' => $this->faker->randomElement(['spouse', 'employee', 'partner', 'family', 'business_associate']),
            'notes' => $this->faker->paragraph,
        ];
    }
}
