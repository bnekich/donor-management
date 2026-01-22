<?php

use App\Models\StripeTransaction;
use App\Processor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

test('rejects request with invalid signature when webhook secret is configured', function () {
    config(['services.stripe.webhook_secret' => 'whsec_test_secret']);

    $payload = [
        'id' => 'evt_test_123',
        'type' => 'payment_intent.succeeded',
        'data' => [
            'object' => [
                'id' => 'pi_test_123',
            ],
        ],
    ];

    Log::shouldReceive('info')->once();
    Log::shouldReceive('warning')->once();

    /** @var \Tests\TestCase $this */
    $response = $this->withHeaders([
        'Stripe-Signature' => 't=1234567890,v1=invalid_signature',
    ])->postJson('/api/stripe/webhook', $payload);

    $response->assertStatus(401)
        ->assertJson([
            'message' => 'Invalid signature',
        ]);

    config(['services.stripe.webhook_secret' => null]);
});

test('accepts request without signature when webhook secret is not configured', function () {
    config(['services.stripe.webhook_secret' => null]);

    $payload = [
        'id' => 'evt_test_123',
        'type' => 'payment_intent.succeeded',
        'data' => [
            'object' => [
                'id' => 'pi_test_123',
            ],
        ],
    ];

    Queue::fake();

    Log::shouldReceive('info')->twice();
    Log::shouldNotReceive('warning');
    Log::shouldNotReceive('error');

    /** @var \Tests\TestCase $this */
    $response = $this->postJson('/api/stripe/webhook', $payload);

    $response->assertSuccessful()
        ->assertJson([
            'message' => 'Webhook processed successfully',
            'event_id' => 'evt_test_123',
            'event_type' => 'payment_intent.succeeded',
        ]);
});

test('successfully stores payment_intent.succeeded event', function () {
    Queue::fake();
    config(['services.stripe.webhook_secret' => null]);

    $payload = [
        'id' => 'evt_payment_intent_123',
        'type' => 'payment_intent.succeeded',
        'data' => [
            'object' => [
                'id' => 'pi_1234567890',
                'amount' => 5000,
                'currency' => 'usd',
                'customer' => 'cus_test_123',
                'description' => 'Test donation',
                'created' => 1686089970,
                'metadata' => [
                    'campaign_id' => '1',
                ],
            ],
        ],
    ];

    Log::shouldReceive('info')->twice();
    Log::shouldNotReceive('warning');
    Log::shouldNotReceive('error');

    /** @var \Tests\TestCase $this */
    $response = $this->postJson('/api/stripe/webhook', $payload);

    $response->assertSuccessful()
        ->assertJson([
            'message' => 'Webhook processed successfully',
            'event_id' => 'evt_payment_intent_123',
            'event_type' => 'payment_intent.succeeded',
        ]);

    $transaction = StripeTransaction::where('stripe_event_id', 'evt_payment_intent_123')->first();

    expect($transaction)->not->toBeNull()
        ->and($transaction->stripe_event_id)->toBe('evt_payment_intent_123')
        ->and($transaction->stripe_object_id)->toBe('pi_1234567890')
        ->and($transaction->event_type)->toBe('payment_intent.succeeded')
        ->and($transaction->status)->toBe('pending')
        ->and($transaction->payload)->toBe($payload);

    Queue::assertPushed(\App\Jobs\ProcessStagedTransaction::class, function ($job) use ($transaction) {
        return $job->transactionId === $transaction->id
            && $job->processor === Processor::Stripe->value;
    });
});

test('successfully stores charge.succeeded event', function () {
    Queue::fake();
    config(['services.stripe.webhook_secret' => null]);

    $payload = [
        'id' => 'evt_charge_123',
        'type' => 'charge.succeeded',
        'data' => [
            'object' => [
                'id' => 'ch_1234567890',
                'amount' => 2500,
                'currency' => 'usd',
                'customer' => 'cus_test_456',
                'description' => 'Charge test',
                'created' => 1686089970,
            ],
        ],
    ];

    Log::shouldReceive('info')->twice();
    Log::shouldNotReceive('warning');
    Log::shouldNotReceive('error');

    /** @var \Tests\TestCase $this */
    $response = $this->postJson('/api/stripe/webhook', $payload);

    $response->assertSuccessful();

    $transaction = StripeTransaction::where('stripe_event_id', 'evt_charge_123')->first();

    expect($transaction)->not->toBeNull()
        ->and($transaction->stripe_event_id)->toBe('evt_charge_123')
        ->and($transaction->stripe_object_id)->toBe('ch_1234567890')
        ->and($transaction->event_type)->toBe('charge.succeeded')
        ->and($transaction->status)->toBe('pending');

    Queue::assertPushed(\App\Jobs\ProcessStagedTransaction::class);
});

test('successfully stores checkout.session.completed event', function () {
    Queue::fake();
    config(['services.stripe.webhook_secret' => null]);

    $payload = [
        'id' => 'evt_checkout_123',
        'type' => 'checkout.session.completed',
        'data' => [
            'object' => [
                'id' => 'cs_test_123',
                'amount_total' => 10000,
                'currency' => 'usd',
                'customer' => 'cus_checkout_123',
                'customer_details' => [
                    'email' => 'test@example.com',
                    'name' => 'Test User',
                ],
                'created' => 1686089970,
                'metadata' => [
                    'campaign_id' => '2',
                ],
            ],
        ],
    ];

    Log::shouldReceive('info')->twice();
    Log::shouldNotReceive('warning');
    Log::shouldNotReceive('error');

    /** @var \Tests\TestCase $this */
    $response = $this->postJson('/api/stripe/webhook', $payload);

    $response->assertSuccessful();

    $transaction = StripeTransaction::where('stripe_event_id', 'evt_checkout_123')->first();

    expect($transaction)->not->toBeNull()
        ->and($transaction->stripe_event_id)->toBe('evt_checkout_123')
        ->and($transaction->stripe_object_id)->toBe('cs_test_123')
        ->and($transaction->event_type)->toBe('checkout.session.completed')
        ->and($transaction->status)->toBe('pending');

    Queue::assertPushed(\App\Jobs\ProcessStagedTransaction::class);
});

test('handles duplicate event gracefully', function () {
    config(['services.stripe.webhook_secret' => null]);

    $existingTransaction = StripeTransaction::factory()->create([
        'stripe_event_id' => 'evt_duplicate_123',
        'status' => 'processed',
    ]);

    $payload = [
        'id' => 'evt_duplicate_123',
        'type' => 'payment_intent.succeeded',
        'data' => [
            'object' => [
                'id' => 'pi_duplicate_123',
            ],
        ],
    ];

    Log::shouldReceive('info')->once();
    Log::shouldReceive('warning')->once();

    /** @var \Tests\TestCase $this */
    $response = $this->postJson('/api/stripe/webhook', $payload);

    $response->assertSuccessful()
        ->assertJson([
            'message' => 'Event already exists',
            'event_id' => 'evt_duplicate_123',
        ]);

    // Verify no duplicate was created
    $count = StripeTransaction::where('stripe_event_id', 'evt_duplicate_123')->count();
    expect($count)->toBe(1);
});

test('validates required fields', function () {
    config(['services.stripe.webhook_secret' => null]);

    /** @var \Tests\TestCase $this */
    $response = $this->postJson('/api/stripe/webhook', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['id', 'type', 'data']);
});

test('validates data.object is required', function () {
    config(['services.stripe.webhook_secret' => null]);

    $payload = [
        'id' => 'evt_test_123',
        'type' => 'payment_intent.succeeded',
        'data' => [],
    ];

    /** @var \Tests\TestCase $this */
    $response = $this->postJson('/api/stripe/webhook', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['data.object']);
});

test('sets status to failed for payment_intent.payment_failed event', function () {
    config(['services.stripe.webhook_secret' => null]);

    $payload = [
        'id' => 'evt_failed_123',
        'type' => 'payment_intent.payment_failed',
        'data' => [
            'object' => [
                'id' => 'pi_failed_123',
            ],
        ],
    ];

    Log::shouldReceive('info')->twice();

    /** @var \Tests\TestCase $this */
    $response = $this->postJson('/api/stripe/webhook', $payload);

    $response->assertSuccessful();

    $transaction = StripeTransaction::where('stripe_event_id', 'evt_failed_123')->first();

    expect($transaction)->not->toBeNull()
        ->and($transaction->status)->toBe('failed');
});

test('sets status to failed for charge.failed event', function () {
    config(['services.stripe.webhook_secret' => null]);

    $payload = [
        'id' => 'evt_charge_failed_123',
        'type' => 'charge.failed',
        'data' => [
            'object' => [
                'id' => 'ch_failed_123',
            ],
        ],
    ];

    Log::shouldReceive('info')->twice();

    /** @var \Tests\TestCase $this */
    $response = $this->postJson('/api/stripe/webhook', $payload);

    $response->assertSuccessful();

    $transaction = StripeTransaction::where('stripe_event_id', 'evt_charge_failed_123')->first();

    expect($transaction)->not->toBeNull()
        ->and($transaction->status)->toBe('failed');
});

test('sets status to pending for unknown event types', function () {
    config(['services.stripe.webhook_secret' => null]);

    $payload = [
        'id' => 'evt_unknown_123',
        'type' => 'customer.created',
        'data' => [
            'object' => [
                'id' => 'cus_unknown_123',
            ],
        ],
    ];

    Log::shouldReceive('info')->twice();

    /** @var \Tests\TestCase $this */
    $response = $this->postJson('/api/stripe/webhook', $payload);

    $response->assertSuccessful();

    $transaction = StripeTransaction::where('stripe_event_id', 'evt_unknown_123')->first();

    expect($transaction)->not->toBeNull()
        ->and($transaction->status)->toBe('pending');
});

test('does not dispatch job for non-processable events', function () {
    Queue::fake();
    config(['services.stripe.webhook_secret' => null]);

    $payload = [
        'id' => 'evt_non_processable_123',
        'type' => 'customer.created',
        'data' => [
            'object' => [
                'id' => 'cus_non_processable_123',
            ],
        ],
    ];

    Log::shouldReceive('info')->twice();

    /** @var \Tests\TestCase $this */
    $response = $this->postJson('/api/stripe/webhook', $payload);

    $response->assertSuccessful();

    Queue::assertNothingPushed();
});

test('handles event without object id gracefully', function () {
    Queue::fake();
    config(['services.stripe.webhook_secret' => null]);

    $payload = [
        'id' => 'evt_no_object_id_123',
        'type' => 'payment_intent.succeeded',
        'data' => [
            'object' => [
                // No id field
                'amount' => 5000,
            ],
        ],
    ];

    Log::shouldReceive('info')->twice();
    Log::shouldNotReceive('warning');
    Log::shouldNotReceive('error');

    /** @var \Tests\TestCase $this */
    $response = $this->postJson('/api/stripe/webhook', $payload);

    $response->assertSuccessful();

    $transaction = StripeTransaction::where('stripe_event_id', 'evt_no_object_id_123')->first();

    expect($transaction)->not->toBeNull()
        ->and($transaction->stripe_object_id)->toBeNull()
        ->and($transaction->status)->toBe('pending');

    Queue::assertPushed(\App\Jobs\ProcessStagedTransaction::class);
});
