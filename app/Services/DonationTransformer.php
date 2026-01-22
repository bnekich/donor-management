<?php

namespace App\Services;

use App\Models\Donation;
use App\Processor;
use App\Services\Processors\GivebutterProcessorExtractor;
use App\Services\Processors\ProcessorExtractor;
use App\Services\Processors\StripeProcessorExtractor;
use Illuminate\Support\Facades\Log;

/**
 * Service for transforming staged transaction data into Donation records.
 */
class DonationTransformer
{
    /**
     * Transform staged transaction into a Donation record.
     *
     * @param  object  $stagedTransaction  Model instance (e.g., GivebutterTransaction)
     * @param  string  $processor  Processor name (e.g., 'givebutter')
     */
    public function transform(object $stagedTransaction, string $processor): ?Donation
    {
        Log::debug('DonationTransformer::transform started', [
            'processor' => $processor,
            'transaction_id' => $stagedTransaction->id ?? null,
            'payload_type' => gettype($stagedTransaction->payload ?? null),
        ]);

        $extractor = $this->getExtractor($processor);

        if (! $extractor) {
            Log::error('No extractor found for processor', [
                'processor' => $processor,
                'transaction_id' => $stagedTransaction->id ?? null,
            ]);

            return null;
        }

        $payload = $stagedTransaction->payload ?? [];

        if (empty($payload)) {
            Log::error('Empty payload in staged transaction', [
                'processor' => $processor,
                'transaction_id' => $stagedTransaction->id ?? null,
            ]);

            return null;
        }

        try {
            $extractedData = $extractor->extract($payload);

            Log::debug('Data extracted from payload', [
                'processor' => $processor,
                'extracted_keys' => array_keys($extractedData),
                'extracted_data' => $extractedData,
            ]);
        } catch (\Exception $e) {
            Log::error('Error during extraction', [
                'processor' => $processor,
                'transaction_id' => $stagedTransaction->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }

        // Validate required fields
        if (! $this->validateExtractedData($extractedData)) {
            $missing = [];
            $required = ['processor', 'processor_id', 'amount', 'transaction_date', 'payment_method'];

            foreach ($required as $field) {
                if (! isset($extractedData[$field]) || $extractedData[$field] === null) {
                    $missing[] = $field;
                }
            }

            Log::error('Extracted data validation failed', [
                'processor' => $processor,
                'transaction_id' => $stagedTransaction->id ?? null,
                'missing_fields' => $missing,
                'extracted_data' => $extractedData,
            ]);

            return null;
        }

        // Check if donation already exists
        $existingDonation = Donation::where('processor', $processor)
            ->where('processor_id', $extractedData['processor_id'] ?? null)
            ->first();

        if ($existingDonation) {
            Log::info('Donation already exists, skipping', [
                'processor' => $processor,
                'processor_id' => $extractedData['processor_id'],
                'donation_id' => $existingDonation->id,
            ]);

            return $existingDonation;
        }

        // Create donation
        try {
            $donation = Donation::create($extractedData);

            Log::info('Donation created successfully', [
                'processor' => $processor,
                'processor_id' => $extractedData['processor_id'],
                'donation_id' => $donation->id,
            ]);

            return $donation;
        } catch (\Exception $e) {
            Log::error('Failed to create donation', [
                'processor' => $processor,
                'processor_id' => $extractedData['processor_id'] ?? null,
                'error' => $e->getMessage(),
                'extracted_data' => $extractedData,
            ]);

            throw $e;
        }
    }

    /**
     * Get the appropriate extractor for the processor.
     */
    protected function getExtractor(string $processor): ?ProcessorExtractor
    {
        return match ($processor) {
            Processor::Givebutter->value => new GivebutterProcessorExtractor,
            Processor::Stripe->value => new StripeProcessorExtractor,
            // Add more processors here as needed
            default => null,
        };
    }

    /**
     * Validate that extracted data has all required fields.
     *
     * @param  array<string, mixed>  $data
     */
    protected function validateExtractedData(array $data): bool
    {
        $required = ['processor', 'processor_id', 'amount', 'transaction_date', 'payment_method'];

        foreach ($required as $field) {
            if (! isset($data[$field]) || $data[$field] === null) {
                return false;
            }
        }

        return true;
    }
}
