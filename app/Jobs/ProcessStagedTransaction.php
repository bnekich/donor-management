<?php

namespace App\Jobs;

use App\Models\GivebutterTransaction;
use App\Models\StripeTransaction;
use App\Services\DonationTransformer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessStagedTransaction implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $transactionId,
        public string $processor = 'givebutter'
    ) {}

    /**
     * Execute the job.
     */
    public function handle(DonationTransformer $transformer): void
    {
        Log::info('ProcessStagedTransaction job started', [
            'transaction_id' => $this->transactionId,
            'processor' => $this->processor,
            'attempt' => $this->attempts(),
        ]);

        try {
            $transaction = $this->getStagedTransaction();

            if (! $transaction) {
                Log::error('Staged transaction not found', [
                    'transaction_id' => $this->transactionId,
                    'processor' => $this->processor,
                    'attempt' => $this->attempts(),
                ]);

                return;
            }

            Log::debug('Transaction found', [
                'transaction_id' => $this->transactionId,
                'transaction_status' => $transaction->status,
                'givebutter_id' => $transaction->givebutter_id ?? null,
            ]);

            // Skip if already processed
            if ($transaction->status === 'processed') {
                Log::info('Transaction already processed', [
                    'transaction_id' => $this->transactionId,
                    'processor' => $this->processor,
                ]);

                return;
            }

            $donation = $transformer->transform($transaction, $this->processor);

            if ($donation) {
                $transaction->update(['status' => 'processed']);
                Log::info('Transaction processed successfully', [
                    'transaction_id' => $this->transactionId,
                    'donation_id' => $donation->id,
                    'processor_id' => $donation->processor_id ?? null,
                ]);
            } else {
                $transaction->update(['status' => 'failed']);
                Log::error('Transaction processing failed - no donation created', [
                    'transaction_id' => $this->transactionId,
                    'processor' => $this->processor,
                    'payload_keys' => array_keys($transaction->payload ?? []),
                ]);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error in ProcessStagedTransaction', [
                'transaction_id' => $this->transactionId,
                'processor' => $this->processor,
                'error' => $e->getMessage(),
                'sql' => $e->getSql() ?? null,
                'bindings' => $e->getBindings() ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        } catch (\Exception $e) {
            Log::error('Transaction processing failed with exception', [
                'transaction_id' => $this->transactionId,
                'processor' => $this->processor,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // Re-throw to trigger retry mechanism
        }
    }

    /**
     * Get the staged transaction model.
     */
    protected function getStagedTransaction(): GivebutterTransaction|StripeTransaction|null
    {
        return match ($this->processor) {
            'givebutter' => GivebutterTransaction::find($this->transactionId),
            'stripe' => StripeTransaction::find($this->transactionId),
            // Add more processors here
            default => null,
        };
    }

    /**
     * Handle a job failure.
     */
    public function failed(?\Throwable $exception): void
    {
        $transaction = $this->getStagedTransaction();

        if ($transaction) {
            $transaction->update(['status' => 'failed']);
        }

        Log::error('ProcessStagedTransaction job failed permanently after all retries', [
            'transaction_id' => $this->transactionId,
            'processor' => $this->processor,
            'total_attempts' => $this->attempts(),
            'error' => $exception?->getMessage(),
            'error_class' => $exception ? get_class($exception) : null,
            'file' => $exception?->getFile(),
            'line' => $exception?->getLine(),
            'trace' => $exception?->getTraceAsString(),
        ]);
    }
}
