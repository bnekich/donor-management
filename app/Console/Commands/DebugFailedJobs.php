<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DebugFailedJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:debug-failed {--limit=10 : Number of failed jobs to show}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display detailed information about failed queue jobs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $limit = (int) $this->option('limit');

        $failedJobs = DB::table('failed_jobs')
            ->orderBy('failed_at', 'desc')
            ->limit($limit)
            ->get();

        if ($failedJobs->isEmpty()) {
            $this->info('No failed jobs found.');

            return self::SUCCESS;
        }

        $this->info("Found {$failedJobs->count()} failed job(s):\n");

        foreach ($failedJobs as $index => $job) {
            $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->line('Failed Job #'.($index + 1));
            $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->line("ID: {$job->id}");
            $this->line("UUID: {$job->uuid}");
            $this->line("Connection: {$job->connection}");
            $this->line("Queue: {$job->queue}");
            $this->line("Failed At: {$job->failed_at}");
            $this->newLine();

            // Decode payload
            $payload = json_decode($job->payload, true);
            if ($payload) {
                $this->line('Job Details:');
                $displayName = $payload['displayName'] ?? 'Unknown';
                $this->line("  Class: {$displayName}");
                if (isset($payload['data']['commandName'])) {
                    $commandData = unserialize($payload['data']['commandName']);
                    if (is_object($commandData)) {
                        $this->line('  Job Class: '.get_class($commandData));
                        if (method_exists($commandData, 'transactionId')) {
                            $this->line("  Transaction ID: {$commandData->transactionId}");
                        }
                        if (method_exists($commandData, 'processor')) {
                            $this->line("  Processor: {$commandData->processor}");
                        }
                    }
                }
                $this->newLine();
            }

            // Display exception
            $this->line('Exception:');
            $exception = json_decode($job->exception, true);
            if ($exception) {
                $errorMessage = $exception['message'] ?? 'Unknown error';
                $this->error("  {$errorMessage}");
                if (isset($exception['file'])) {
                    $this->line("  File: {$exception['file']}");
                }
                if (isset($exception['line'])) {
                    $this->line("  Line: {$exception['line']}");
                }
                if (isset($exception['trace'])) {
                    $this->newLine();
                    $this->line('  Stack Trace (first 10 lines):');
                    $traceLines = explode("\n", $exception['trace']);
                    foreach (array_slice($traceLines, 0, 10) as $traceLine) {
                        $this->line("    {$traceLine}");
                    }
                }
            } else {
                $this->line("  {$job->exception}");
            }

            $this->newLine(2);
        }

        return self::SUCCESS;
    }
}
