<?php

// FundingRequestSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FundingRequest;

class FundingRequestSeeder extends Seeder
{
    public function run(): void
    {
        FundingRequest::factory(10)->create();
    }
}
