<?php

namespace App\Services\Processors;

use App\Models\Campaign;
use App\Models\Donor;
use App\Models\DonorDetail;

/**
 * Extractor for Givebutter webhook payloads.
 */
class GivebutterProcessorExtractor extends ProcessorExtractor
{
    public function getProcessorName(): string
    {
        return 'givebutter';
    }

    /**
     * Extract standardized donation data from Givebutter payload.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function extract(array $payload): array
    {
        // Handle both wrapped and unwrapped payloads
        $data = $payload['data'] ?? $payload;
        $mappings = $this->getMappings();
        $extracted = [];

        // Process each mapping
        foreach ($mappings as $mapping) {
            // Try both with and without 'data.' prefix for flexibility
            $sourceValue = $this->getValueByPath($data, $mapping->source_field);

            // If not found, try without 'data.' prefix
            if ($sourceValue === null && str_starts_with($mapping->source_field, 'data.')) {
                $alternatePath = str_replace('data.', '', $mapping->source_field);
                $sourceValue = $this->getValueByPath($data, $alternatePath);
            }

            // Skip if required field is missing
            if ($mapping->is_required && $sourceValue === null) {
                continue;
            }

            $transformedValue = $this->transformValue(
                $sourceValue,
                $mapping->transformation_type,
                $mapping->transformation_config
            );

            $extracted[$mapping->target_field] = $transformedValue;
        }

        // Apply processor-specific logic
        $extracted = $this->applyProcessorSpecificLogic($data, $extracted);

        return $extracted;
    }

    /**
     * Apply Givebutter-specific extraction logic.
     *
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $extracted
     * @return array<string, mixed>
     */
    protected function applyProcessorSpecificLogic(array $data, array $extracted): array
    {
        // Set processor name
        $extracted['processor'] = 'givebutter';
        $extracted['processor_id'] = $data['id'] ?? null;

        // Extract donor information and find/create donor
        if (isset($data['donor'])) {
            $donor = $this->findOrCreateDonor($data['donor']);
            if ($donor) {
                $extracted['donor_id'] = $donor->id;
            }
        }

        // Find campaign by external ID if provided
        if (isset($data['campaign_id'])) {
            $campaign = Campaign::where('name', 'like', '%'.$data['campaign_id'].'%')
                ->orWhere('id', $data['campaign_id'])
                ->first();

            if ($campaign) {
                $extracted['campaign_id'] = $campaign->id;
            }
        }

        // Extract transaction date
        if (isset($data['created_at'])) {
            $extracted['transaction_date'] = date('Y-m-d', strtotime($data['created_at']));
        } else {
            $extracted['transaction_date'] = now()->toDateString();
        }

        // Extract payment method
        $extracted['payment_method'] = $this->determinePaymentMethod($data);

        // Calculate net amount if not already set
        if (! isset($extracted['net_amount']) && isset($extracted['amount'])) {
            $fee = $extracted['processor_fee'] ?? 0;
            $extracted['net_amount'] = max(0, (float) $extracted['amount'] - (float) $fee);
        }

        return $extracted;
    }

    /**
     * Find or create donor from Givebutter donor data.
     *
     * @param  array<string, mixed>  $donorData
     */
    protected function findOrCreateDonor(array $donorData): ?Donor
    {
        $email = $donorData['email'] ?? null;

        if (! $email) {
            return null;
        }

        // Find or create Donor by email
        $donor = Donor::firstOrCreate(
            ['email' => $email],
            ['phone' => $donorData['phone'] ?? null]
        );

        // Ensure Donor has a linked DonorDetail (Givebutter donor data is individual)
        if (! $donor->donorDetail) {
            $name = $donorData['name'] ?? '';
            $nameParts = $this->parseName($name);

            DonorDetail::create([
                'donor_id' => $donor->id,
                'first_name' => $nameParts['first_name'],
                'last_name' => $nameParts['last_name'],
            ]);
        }

        return $donor;
    }

    /**
     * Parse full name into first and last name.
     *
     * @return array{first_name: string, last_name: string}
     */
    protected function parseName(string $name): array
    {
        $parts = explode(' ', trim($name), 2);

        return [
            'first_name' => $parts[0] ?? '',
            'last_name' => $parts[1] ?? '',
        ];
    }

    /**
     * Determine payment method from Givebutter data.
     */
    protected function determinePaymentMethod(array $data): string
    {
        $paymentMethod = strtolower($data['payment_method'] ?? $data['method'] ?? 'credit_card');

        return match (true) {
            str_contains($paymentMethod, 'card') => 'credit_card',
            str_contains($paymentMethod, 'ach') || str_contains($paymentMethod, 'bank') => 'direct',
            str_contains($paymentMethod, 'check') => 'check',
            default => 'credit_card',
        };
    }
}
