<?php

namespace Database\Seeders;

use App\Models\DonorDetail;
use Illuminate\Database\Seeder;

class IndividualSeeder extends Seeder
{
    public function run(): void
    {
        DonorDetail::factory(50)->create();
    }
}
