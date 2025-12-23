<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\GivebutterTransaction;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GivebutterTransaction>
 */
class GivebutterTransactionFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = GivebutterTransaction::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'givebutter_id' => $this->faker->unique()->uuid(),
      'payload' => [
        'id' => $this->faker->uuid(),
        'amount' => $this->faker->randomFloat(2, 10, 1000),
        'currency' => 'USD',
        'status' => 'completed',
        'donor' => [
          'name' => $this->faker->name(),
          'email' => $this->faker->email(),
        ],
        'campaign_id' => $this->faker->numberBetween(1, 10),
        'created_at' => now()->toISOString(),
      ],
      'status' => 'pending',
    ];
  }
}
