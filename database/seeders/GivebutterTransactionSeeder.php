<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GivebutterTransaction;

class GivebutterTransactionSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    GivebutterTransaction::factory(10)->create();
  }
}
