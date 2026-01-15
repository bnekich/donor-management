<?php

namespace App\Jobs;

use App\Models\GivebutterTransaction;
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
        $transaction = $this->getStagedTransaction();

        if (! $transaction) {
            Log::warning('Staged transaction not found', [
                'transaction_id' => $this->transactionId,
                'processor' => $this->processor,
            ]);

            return;
        }

        // Skip if already processed
        if ($transaction->status === 'processed') {
            Log::info('Transaction already processed', [
                'transaction_id' => $this->transactionId,
                'processor' => $this->processor,
            ]);

            return;
        }

        try {
            $donation = $transformer->transform($transaction, $this->processor);

            if ($donation) {
                $transaction->update(['status' => 'processed']);
                Log::info('Transaction processed successfully', [
                    'transaction_id' => $this->transactionId,
                    'donation_id' => $donation->id,
                ]);
            } else {
                $transaction->update(['status' => 'failed']);
                Log::warning('Transaction processing failed - no donation created', [
                    'transaction_id' => $this->transactionId,
                ]);
            }
        } catch (\Exception $e) {
            $transaction->update(['status' => 'failed']);
            Log::error('Transaction processing failed with exception', [
                'transaction_id' => $this->transactionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // Re-throw to trigger retry mechanism
        }
    }

    /**
     * Get the staged transaction model.
     */
    protected function getStagedTransaction(): ?GivebutterTransaction
    {
        return match ($this->processor) {
            'givebutter' => GivebutterTransaction::find($this->transactionId),
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

        Log::error('ProcessStagedTransaction job failed permanently', [
            'transaction_id' => $this->transactionId,
            'processor' => $this->processor,
            'error' => $exception?->getMessage(),
        ]);
    }
}
