<?php

// ChartOfAccountSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChartOfAccount;

class ChartOfAccountSeeder extends Seeder
{
    public function run(): void
    {
        ChartOfAccount::factory(15)->create();
    }
}
