<?php

namespace Database\Factories;

use App\Models\Donor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pledge>
 */
class PledgeFactory extends Factory
{
    public function definition(): array
    {
        $donor = Donor::factory()->has(\App\Models\DonorDetail::factory())->create();

        return [
            'donor_id' => $donor->id,
            'amount' => $this->faker->randomFloat(2, 50, 5000),
            'pledge_date' => $this->faker->dateTimeThisYear()->format('Y-m-d'),
            'due_date' => $this->faker->optional()->dateTimeThisYear('+1 year')?->format('Y-m-d'),
            'status' => $this->faker->randomElement(['pending', 'fulfilled', 'cancelled']),
            'campaign_id' => null,
            'chapter_id' => null,
            'account_id' => null,
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }
}
