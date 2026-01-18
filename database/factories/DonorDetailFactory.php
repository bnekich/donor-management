<?php

namespace Database\Factories;

use App\Models\Donor;
use App\Models\DonorDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DonorDetail>
 */
class DonorDetailFactory extends Factory
{
    protected $model = DonorDetail::class;

    public function definition(): array
    {
        return [
            'donor_id' => Donor::factory(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'birthday' => $this->faker->optional()->date('Y-m-d', 'now'),
            'occupation' => $this->faker->optional()->jobTitle(),
            'organization_id' => null,
        ];
    }
}
