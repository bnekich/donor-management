<?php

namespace Database\Factories;

use App\Models\StripeTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StripeTransaction>
 */
class StripeTransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StripeTransaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $eventId = 'evt_'.$this->faker->unique()->bothify('##########');
        $objectId = 'pi_'.$this->faker->bothify('##########');

        return [
            'stripe_event_id' => $eventId,
            'stripe_object_id' => $objectId,
            'event_type' => 'payment_intent.succeeded',
            'payload' => [
                'id' => $eventId,
                'object' => 'event',
                'type' => 'payment_intent.succeeded',
                'data' => [
                    'object' => [
                        'id' => $objectId,
                        'object' => 'payment_intent',
                        'amount' => $this->faker->randomElement([1000, 2500, 5000, 10000]),
                        'currency' => 'usd',
                        'customer' => 'cus_'.$this->faker->bothify('##########'),
                        'created' => now()->timestamp,
                    ],
                ],
            ],
            'status' => 'pending',
        ];
    }
}
