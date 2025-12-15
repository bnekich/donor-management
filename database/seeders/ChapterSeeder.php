<?php

// ChapterSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Chapter;

class ChapterSeeder extends Seeder
{
    public function run(): void
    {
        Chapter::factory(10)->create();
    }
}
