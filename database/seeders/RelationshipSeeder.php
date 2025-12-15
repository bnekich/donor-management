<?php
// RelationshipSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Relationship;

class RelationshipSeeder extends Seeder
{
    public function run(): void
    {
        Relationship::factory(20)->create();
    }
}
