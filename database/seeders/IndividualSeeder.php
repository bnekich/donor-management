<?php

// IndividualSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Individual;

class IndividualSeeder extends Seeder
{
    public function run(): void
    {
        Individual::factory(50)->create();
    }
}
