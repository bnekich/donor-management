<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\OrganizationSeeder;
use Database\Seeders\IndividualSeeder;
use Database\Seeders\CampaignSeeder;
use Database\Seeders\ChapterSeeder;
use Database\Seeders\ChartOfAccountSeeder;
use Database\Seeders\PledgeSeeder;
use Database\Seeders\DonationSeeder;
use Database\Seeders\NoteSeeder;
use Database\Seeders\FundingRequestSeeder;
use Database\Seeders\RelationshipSeeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            OrganizationSeeder::class,
            IndividualSeeder::class,
            CampaignSeeder::class,
            ChapterSeeder::class,
            ChartOfAccountSeeder::class,
            PledgeSeeder::class,
            DonationSeeder::class,
            NoteSeeder::class,
            FundingRequestSeeder::class,
            RelationshipSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Development User',
            'email' => 'dev@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}
