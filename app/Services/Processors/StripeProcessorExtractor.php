<?php

namespace App\Services\Processors;

use App\Models\Campaign;
use App\Models\Donor;
use App\Models\DonorDetail;

/**
 * Extractor for Stripe webhook payloads.
 */
class StripeProcessorExtractor extends ProcessorExtractor
{
    public function getProcessorName(): string
    {
        return 'stripe';
    }

    /**
     * Extract standardized donation data from Stripe payload.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function extract(array $payload): array
    {
        // Stripe events have data.object containing the actual payment object
        $eventData = $payload['data'] ?? [];
        $objectData = $eventData['object'] ?? $payload;
        $mappings = $this->getMappings();
        $extracted = [];

        // Process each mapping
        foreach ($mappings as $mapping) {
            // Try to get value from the object data
            $sourceValue = $this->getValueByPath($objectData, $mapping->source_field);

            // If not found, try from the full payload
            if ($sourceValue === null) {
                $sourceValue = $this->getValueByPath($payload, $mapping->source_field);
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
        $extracted = $this->applyProcessorSpecificLogic($objectData, $extracted, $payload);

        return $extracted;
    }

    /**
     * Apply Stripe-specific extraction logic.
     *
     * @param  array<string, mixed>  $objectData
     * @param  array<string, mixed>  $extracted
     * @param  array<string, mixed>  $fullPayload
     * @return array<string, mixed>
     */
    protected function applyProcessorSpecificLogic(array $objectData, array $extracted, array $fullPayload): array
    {
        // Set processor name
        $extracted['processor'] = 'stripe';
        $extracted['processor_id'] = $objectData['id'] ?? $fullPayload['id'] ?? null;

        // Extract amount (Stripe amounts are in cents)
        if (isset($objectData['amount'])) {
            $extracted['amount'] = $objectData['amount'] / 100; // Convert cents to dollars
        } elseif (isset($objectData['amount_total'])) {
            $extracted['amount'] = $objectData['amount_total'] / 100;
        }

        // Extract fees
        if (isset($objectData['balance_transaction'])) {
            // If balance_transaction is an object with fee details
            if (is_array($objectData['balance_transaction']) && isset($objectData['balance_transaction']['fee'])) {
                $extracted['processor_fee'] = $objectData['balance_transaction']['fee'] / 100;
            }
        }

        // Calculate net amount
        if (isset($extracted['amount']) && isset($extracted['processor_fee'])) {
            $extracted['net_amount'] = max(0, (float) $extracted['amount'] - (float) $extracted['processor_fee']);
        } elseif (isset($extracted['amount'])) {
            $extracted['net_amount'] = (float) $extracted['amount'];
        }

        // Extract customer/donor information
        if (isset($objectData['customer'])) {
            $customerId = $objectData['customer'];
            $donor = $this->findOrCreateDonorFromStripeCustomer($customerId, $objectData);
            if ($donor) {
                $extracted['donor_id'] = $donor->id;
            }
        } elseif (isset($objectData['customer_details'])) {
            $donor = $this->findOrCreateDonorFromCustomerDetails($objectData['customer_details']);
            if ($donor) {
                $extracted['donor_id'] = $donor->id;
            }
        }

        // Extract transaction date (Stripe uses Unix timestamps)
        if (isset($objectData['created'])) {
            $extracted['transaction_date'] = date('Y-m-d', $objectData['created']);
        } elseif (isset($fullPayload['created'])) {
            $extracted['transaction_date'] = date('Y-m-d', $fullPayload['created']);
        } else {
            $extracted['transaction_date'] = now()->toDateString();
        }

        // Extract payment method
        $extracted['payment_method'] = $this->determinePaymentMethod($objectData);

        // Extract metadata (campaign, notes, etc.)
        if (isset($objectData['metadata'])) {
            $metadata = $objectData['metadata'];

            if (isset($metadata['campaign_id'])) {
                $campaign = Campaign::find($metadata['campaign_id']);
                if ($campaign) {
                    $extracted['campaign_id'] = $campaign->id;
                }
            }

            if (isset($metadata['notes'])) {
                $extracted['notes'] = $metadata['notes'];
            }
        }

        // Extract description as notes if no notes in metadata
        if (! isset($extracted['notes']) && isset($objectData['description'])) {
            $extracted['notes'] = $objectData['description'];
        }

        return $extracted;
    }

    /**
     * Find or create donor from Stripe customer ID.
     * Note: This would typically require fetching customer data from Stripe API.
     * For now, we'll try to find by email if available in the object data.
     *
     * @param  array<string, mixed>  $objectData
     */
    protected function findOrCreateDonorFromStripeCustomer(string $customerId, array $objectData): ?Donor
    {
        // Try to get email from various places in the object
        $email = $objectData['receipt_email']
            ?? $objectData['customer_details']['email']
            ?? $objectData['billing_details']['email']
            ?? null;

        if (! $email) {
            return null;
        }

        $donor = Donor::firstOrCreate(
            ['email' => $email],
            ['phone' => $objectData['billing_details']['phone'] ?? null]
        );

        if (! $donor->donorDetail) {
            $name = $objectData['customer_details']['name']
                ?? $objectData['billing_details']['name']
                ?? '';
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
     * Find or create donor from customer details.
     *
     * @param  array<string, mixed>  $customerDetails
     */
    protected function findOrCreateDonorFromCustomerDetails(array $customerDetails): ?Donor
    {
        $email = $customerDetails['email'] ?? null;

        if (! $email) {
            return null;
        }

        $donor = Donor::firstOrCreate(
            ['email' => $email],
            []
        );

        if (! $donor->donorDetail) {
            $name = $customerDetails['name'] ?? '';
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
     * Determine payment method from Stripe data.
     */
    protected function determinePaymentMethod(array $objectData): string
    {
        $paymentMethodType = $objectData['payment_method_types'][0] ??
            $objectData['payment_method_details']['type'] ??
            $objectData['type'] ??
            'card';

        return match ($paymentMethodType) {
            'card', 'pm_card' => 'credit_card',
            'us_bank_account', 'acss_debit', 'sepa_debit' => 'direct',
            'check' => 'check',
            default => 'credit_card',
        };
    }
}
