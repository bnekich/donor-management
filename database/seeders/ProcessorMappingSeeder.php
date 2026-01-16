<?php

namespace Database\Seeders;

use App\Models\ProcessorMapping;
use Illuminate\Database\Seeder;

class ProcessorMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mappings = [
            // Givebutter mappings
            [
                'processor' => 'givebutter',
                'source_field' => 'data.id',
                'target_field' => 'processor_id',
                'transformation_type' => 'direct',
                'is_required' => true,
                'priority' => 1,
            ],
            [
                'processor' => 'givebutter',
                'source_field' => 'data.amount',
                'target_field' => 'amount',
                'transformation_type' => 'direct',
                'is_required' => true,
                'priority' => 2,
            ],
            [
                'processor' => 'givebutter',
                'source_field' => 'data.fee',
                'target_field' => 'processor_fee',
                'transformation_type' => 'direct',
                'is_required' => false,
                'priority' => 3,
            ],
            [
                'processor' => 'givebutter',
                'source_field' => 'data.reference_number',
                'target_field' => 'reference_number',
                'transformation_type' => 'direct',
                'is_required' => false,
                'priority' => 4,
            ],
            [
                'processor' => 'givebutter',
                'source_field' => 'data.transaction_id',
                'target_field' => 'transaction_id',
                'transformation_type' => 'direct',
                'is_required' => false,
                'priority' => 5,
            ],
            [
                'processor' => 'givebutter',
                'source_field' => 'data.notes',
                'target_field' => 'notes',
                'transformation_type' => 'direct',
                'is_required' => false,
                'priority' => 6,
            ],
            // Stripe mappings
            [
                'processor' => 'stripe',
                'source_field' => 'id',
                'target_field' => 'processor_id',
                'transformation_type' => 'direct',
                'is_required' => true,
                'priority' => 1,
            ],
            [
                'processor' => 'stripe',
                'source_field' => 'description',
                'target_field' => 'notes',
                'transformation_type' => 'direct',
                'is_required' => false,
                'priority' => 2,
            ],
            [
                'processor' => 'stripe',
                'source_field' => 'metadata.notes',
                'target_field' => 'notes',
                'transformation_type' => 'direct',
                'is_required' => false,
                'priority' => 3,
            ],
        ];

        foreach ($mappings as $mapping) {
            ProcessorMapping::updateOrCreate(
                [
                    'processor' => $mapping['processor'],
                    'target_field' => $mapping['target_field'],
                    'source_field' => $mapping['source_field'],
                ],
                $mapping
            );
        }
    }
}
