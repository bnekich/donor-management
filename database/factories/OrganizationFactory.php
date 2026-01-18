<?php

namespace Database\Factories;

use App\Models\Donor;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition(): array
    {
        return [
            'donor_id' => Donor::factory(),
            'name' => $this->faker->company(),
            'website' => $this->faker->optional()->url(),
            'description' => $this->faker->optional()->paragraph(),
            'type' => $this->faker->randomElement(['corporate_donor', 'chapter', 'grant_recipient']),
            'needs_review' => $this->faker->boolean(20),
        ];
    }
}
