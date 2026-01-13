<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GivebutterTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GivebutterWebhookController extends Controller
{
    /**
     * Store the incoming Givebutter webhook payload.
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        // Log the incoming request for debugging
        Log::info('Givebutter Webhook Received', [
            'headers' => $request->headers->all(),
            'payload' => $request->all(),
        ]);

        // Validate webhook signature
        $signature = $request->header('Signature');
        $expectedSignature = config('services.givebutter.webhook_signature');

        if ($signature !== $expectedSignature) {
            Log::warning('Invalid Givebutter webhook signature', [
                'received' => $signature,
                'expected' => $expectedSignature,
            ]);

            return response()->json([
                'message' => 'Invalid signature',
            ], 401);
        }

        // Validate the request
        $request->validate([
            'id' => 'required|string',
            'event' => 'required|string',
            'data' => 'required|array',
            'data.id' => 'required|string',
        ]);

        $webhookId = $request->input('id');
        $event = $request->input('event');
        $transactionData = $request->input('data');
        $transactionId = $transactionData['id'];

        // Check if transaction already exists
        $existing = GivebutterTransaction::where('givebutter_id', $transactionId)->first();
        if ($existing) {
            Log::warning('Duplicate Givebutter transaction received', [
                'webhook_id' => $webhookId,
                'transaction_id' => $transactionId,
            ]);

            return response()->json([
                'message' => 'Transaction already exists',
                'transaction_id' => $transactionId,
            ], 200);
        }

        // Determine status based on event type
        $status = match ($event) {
            'transaction.succeeded' => 'processed',
            'transaction.failed' => 'failed',
            default => 'pending',
        };

        // Store the transaction
        GivebutterTransaction::create([
            'givebutter_id' => $transactionId,
            'payload' => $request->all(),
            'status' => $status,
        ]);

        Log::info('Givebutter transaction stored', [
            'webhook_id' => $webhookId,
            'transaction_id' => $transactionId,
            'event' => $event,
            'status' => $status,
        ]);

        return response()->json([
            'message' => 'Webhook processed successfully',
            'transaction_id' => $transactionId,
            'event' => $event,
        ], 200);
    }
}
