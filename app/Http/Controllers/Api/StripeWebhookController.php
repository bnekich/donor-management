<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessStagedTransaction;
use App\Models\StripeTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    /**
     * Store the incoming Stripe webhook payload.
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        // Log the incoming request for debugging
        Log::info('Stripe Webhook Received', [
            'headers' => $request->headers->all(),
            'event_id' => $request->input('id'),
            'event_type' => $request->input('type'),
        ]);

        // Validate webhook signature
        $signature = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        if ($webhookSecret && ! $this->verifySignature($request, $signature, $webhookSecret)) {
            Log::warning('Invalid Stripe webhook signature', [
                'received' => $signature,
            ]);

            return response()->json([
                'message' => 'Invalid signature',
            ], 401);
        }

        // Validate the request
        $request->validate([
            'id' => 'required|string',
            'type' => 'required|string',
            'data' => 'required|array',
            'data.object' => 'required|array',
        ]);

        $eventId = $request->input('id');
        $eventType = $request->input('type');
        $eventData = $request->input('data');
        $objectData = $eventData['object'] ?? [];
        $objectId = $objectData['id'] ?? null;

        // Check if event already exists
        $existing = StripeTransaction::where('stripe_event_id', $eventId)->first();
        if ($existing) {
            Log::warning('Duplicate Stripe event received', [
                'event_id' => $eventId,
                'event_type' => $eventType,
            ]);

            return response()->json([
                'message' => 'Event already exists',
                'event_id' => $eventId,
            ], 200);
        }

        // Determine status based on event type
        // Only process payment-related events that indicate success
        $status = match ($eventType) {
            'payment_intent.succeeded',
            'charge.succeeded',
            'checkout.session.completed' => 'pending', // Will be processed by job
            'payment_intent.payment_failed',
            'charge.failed' => 'failed',
            default => 'pending',
        };

        // Store the transaction
        $transaction = StripeTransaction::create([
            'stripe_event_id' => $eventId,
            'stripe_object_id' => $objectId,
            'event_type' => $eventType,
            'payload' => $request->all(),
            'status' => $status,
        ]);

        Log::info('Stripe event stored', [
            'event_id' => $eventId,
            'event_type' => $eventType,
            'object_id' => $objectId,
            'status' => $status,
        ]);

        // Dispatch job to process the transaction asynchronously
        // Only process successful payment events
        $processableEvents = [
            'payment_intent.succeeded',
            'charge.succeeded',
            'checkout.session.completed',
        ];

        if (in_array($eventType, $processableEvents)) {
            ProcessStagedTransaction::dispatch($transaction->id, 'stripe');
        }

        return response()->json([
            'message' => 'Webhook processed successfully',
            'event_id' => $eventId,
            'event_type' => $eventType,
        ], 200);
    }

    /**
     * Verify Stripe webhook signature.
     * For production, consider using the Stripe PHP SDK for proper signature verification.
     */
    protected function verifySignature(Request $request, ?string $signature, string $secret): bool
    {
        if (! $signature || ! $secret) {
            return false;
        }

        // Basic signature verification
        // For production, use Stripe's SDK: \Stripe\Webhook::constructEvent()
        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        // Stripe sends signatures in format: t=timestamp,v1=signature
        // Extract the signature part
        $signatureParts = explode(',', $signature);
        $receivedSignature = null;

        foreach ($signatureParts as $part) {
            if (str_starts_with($part, 'v1=')) {
                $receivedSignature = substr($part, 3);
                break;
            }
        }

        if (! $receivedSignature) {
            return false;
        }

        return hash_equals($expectedSignature, $receivedSignature);
    }
}
