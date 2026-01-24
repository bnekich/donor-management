<?php

namespace App\Services\Processors;

use App\Models\Campaign;
use App\Models\Donor;
use App\Models\DonorDetail;
use App\Processor;
use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\Log;

/**
 * Extractor for Givebutter webhook payloads.
 */
class GivebutterProcessorExtractor extends ProcessorExtractor
{
    public function getProcessorName(): string
    {
        return Processor::Givebutter->value;
    }

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

    protected function applyProcessorSpecificLogic(array $data, array $extracted): array
    {
        // Set processor name
        $extracted['processor'] = Processor::Givebutter->value;
        $extracted['processor_id'] = $data['id'] ?? null;
        Log::debug('Create donor if email is present: ' . $data['email'] ?? 'no email');
        //Extract donor information and find/create donor
        if (isset($data['email'])) {
            $donor = $this->findOrCreateDonor($data);
            if ($donor) {
                $extracted['donor_id'] = $donor->id;
            }
        }

        // Find campaign by external ID if provided
        if (isset($data['campaign_id'])) {
            $campaign = $this->findOrCreateCampaign($data);
            if ($campaign) {
                $extracted['campaign_id'] = $campaign->id;
            }
        }

        // Find fund by external ID if provided
        if (isset($data['fund_id'])) {
            $fund = $this->findOrCreateChartOfAccount($data);
            if ($fund) {
                $extracted['fund_id'] = $fund->id;
            }
        }

        // Extract transaction date
        if (isset($data['created_at'])) {
            $extracted['transaction_date'] = date('Y-m-d', strtotime($data['created_at']));
        } else {
            $extracted['transaction_date'] = now()->toDateString();
        }

        // Extract payment method
        // $extracted['payment_method'] = $this->determinePaymentMethod($data);

        // Calculate net amount if not already set
        // if (! isset($extracted['net_amount']) && isset($extracted['amount'])) {
        //     $fee = $extracted['processor_fee'] ?? 0;
        //     $extracted['net_amount'] = max(0, (float) $extracted['amount'] - (float) $fee);
        // }

        return $extracted;
    }
    protected function findOrCreateChartOfAccount(array $chartOfAccountData): ?ChartOfAccount
    {
        $processor_id = $chartOfAccountData['fund_id'] ?? null;

        if (! $processor_id) {
            return null;
        }

        $chartOfAccount = ChartOfAccount::updateOrCreate(
            ['processor_id' => $processor_id],
            [
                'processor' => Processor::Givebutter->value,
                'code' => $chartOfAccountData['fund_code'] ?? null,
                'type' => 'income',
            ]
        );

        // TODO call Givebutter's API to get the fund details
        return $chartOfAccount;
    }

    protected function findOrCreateCampaign(array $campaignData): ?Campaign
    {
        // capture campaign_id from the campaignData, if its not there, return null
        $processor_id = $campaignData['campaign_id'] ?? null;
        if (! $processor_id) {
            return null;
        }

       $campaign = Campaign::updateOrCreate(
            ['processor_id' => $processor_id],
            [
                'processor' => Processor::Givebutter->value,
                'name' => $campaignData['campaign_title'] ?? null,
                'code' => $campaignData['campaign_code'] ?? null,
            ]
        );

        // TODO call Givebutter's API to get the campaign details and update the campaign 

         return $campaign;
    }

    protected function findOrCreateDonor(array $donorData): ?Donor
    {
        $email = $donorData['email'] ?? null;
        $processor_id = $donorData['contact_id'] ?? null;

        if (! $email || ! $processor_id) {
            return null;
        }
        // Extract address data from nested address object
        $address = $donorData['address'] ?? [];
        $donor = Donor::updateOrCreate(
            ['email' => $email,'processor_id' => $processor_id],
            [
                'processor' => Processor::Givebutter->value,
                'first_name' => $donorData['first_name'] ?? null,
                'last_name' => $donorData['last_name'] ?? null,
                'phone' => $donorData['phone'] ?? null,
                'address_line1' => $address['address_1'] ?? null,
                'address_line2' => $address['address_2'] ?? null,
                'city' => $address['city'] ?? null,
                'state' => $address['state'] ?? null,
                'zip' => $address['zipcode'] ?? null,
                'country' => $address['country'] ?? null,
            ]
        );

        // Ensure Donor has a linked DonorDetail (Givebutter donor data is individual)
        if (! $donor->donorDetail) {
            DonorDetail::updateOrCreate(
                ['donor_id' => $donor->id],
                [
                'can_be_contacted' => $donorData['communication_opt_in'] ?? false,
            ]);
        }

        return $donor;
    }

    /*     protected function determinePaymentMethod(array $data): string
        {
            $paymentMethod = strtolower($data['payment_method'] ?? $data['method'] ?? 'credit_card');

            return match (true) {
                str_contains($paymentMethod, 'card') => 'credit_card',
                str_contains($paymentMethod, 'ach') || str_contains($paymentMethod, 'bank') => 'direct',
                str_contains($paymentMethod, 'check') => 'check',
                default => 'credit_card',
            };
        }
    */
}
