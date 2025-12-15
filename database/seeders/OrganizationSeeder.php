<?php

// OrganizationSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organization;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        Organization::factory(20)->create();
    }
}
