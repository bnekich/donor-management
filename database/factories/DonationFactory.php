<?php

namespace Database\Factories;

use App\Models\Donor;
use App\Processor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Donation>
 */
class DonationFactory extends Factory
{
    public function definition(): array
    {
        $donor = Donor::factory()->has(\App\Models\DonorDetail::factory())->create();

        return [
            'processor' => $this->faker->randomElement([Processor::Givebutter->value, Processor::Stripe->value]),
            'processor_id' => $this->faker->optional()->uuid(),
            'reference_number' => $this->faker->optional()->numerify('REF-########'),
            'donor_id' => $donor->id,
            'amount' => $this->faker->randomFloat(2, 50, 5000),
            'processor_fee' => $this->faker->optional()->randomFloat(2, 0, 50),
            'net_amount' => null,
            'transaction_date' => $this->faker->dateTimeThisYear()->format('Y-m-d'),
            'payment_method' => $this->faker->randomElement(['credit_card', 'check', 'direct', 'paypal']),
            'transaction_id' => $this->faker->optional()->uuid(),
            'pledge_id' => null,
            'campaign_id' => null,
            'chapter_id' => null,
            'account_id' => null,
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }
}
