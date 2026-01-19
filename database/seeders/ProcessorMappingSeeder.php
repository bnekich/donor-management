<?php

namespace Database\Seeders;

use App\Models\ProcessorMapping;
use Illuminate\Database\Seeder;

class ProcessorMappingSeeder extends Seeder
{
    public function run(): void
    {
        $mappings = [
            // Givebutter mappings
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
                'source_field' => 'data.id',
                'target_field' => 'processor_id',
                'transformation_type' => 'direct',
                'is_required' => true,
                'priority' => 1,
            ],
            [
                'processor' => 'givebutter',
                'source_field' => 'data.is_recurring',
                'target_field' => 'is_recurring',
                'transformation_type' => 'direct',
                'is_required' => false,
                'priority' => 4,
            ],
            [
                'processor' => 'givebutter',
                'source_field' => 'data.method',
                'target_field' => 'payment_method',
                'transformation_type' => 'direct',
                'is_required' => false,
                'priority' => 5,
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
