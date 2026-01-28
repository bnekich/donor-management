<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\ProcessorMappingSeeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            // ChapterSeeder::class,
            // ChartOfAccountSeeder::class,
            // FundingRequestSeeder::class,
            // RelationshipSeeder::class,
            ProcessorMappingSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Development User',
            'email' => 'dev@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}
