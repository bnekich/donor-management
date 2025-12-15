<?php

// PledgeSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pledge;

class PledgeSeeder extends Seeder
{
    public function run(): void
    {
        Pledge::factory(30)->create();
    }
}
