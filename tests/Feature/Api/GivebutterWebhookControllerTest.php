<?php

use App\Models\GivebutterTransaction;
use Illuminate\Support\Facades\Log;

test('successfully stores a new transaction from webhook', function () {
    $payload = [
        'id' => 'b7ee9e36-cc07-479c-a34b-b271c0a6fae7',
        'event' => 'transaction.succeeded',
        'data' => [
            'id' => 'ZZPcHQfpqEigZhnK',
            'amount' => 50,
            'status' => 'succeeded',
            'method' => 'check',
            'first_name' => 'Megan',
            'last_name' => 'Javornik',
            'email' => null,
            'phone' => null,
            'campaign_id' => 230428,
            'created_at' => '2026-01-12T15:59:26+00:00',
        ],
    ];

    Log::shouldReceive('info')->twice();
    Log::shouldNotReceive('warning');

    $response = $this->postJson('/api/givebutter/webhook', $payload);

    $response->assertSuccessful()
        ->assertJson([
            'message' => 'Webhook processed successfully',
            'transaction_id' => 'ZZPcHQfpqEigZhnK',
            'event' => 'transaction.succeeded',
        ]);

    $transaction = GivebutterTransaction::where('givebutter_id', 'ZZPcHQfpqEigZhnK')->first();

    expect($transaction)->not->toBeNull()
        ->and($transaction->givebutter_id)->toBe('ZZPcHQfpqEigZhnK')
        ->and($transaction->status)->toBe('processed')
        ->and($transaction->payload)->toBe($payload);
});

test('handles duplicate transaction gracefully', function () {
    $existingTransaction = GivebutterTransaction::factory()->create([
        'givebutter_id' => 'ZZPcHQfpqEigZhnK',
        'status' => 'processed',
    ]);

    $payload = [
        'id' => 'b7ee9e36-cc07-479c-a34b-b271c0a6fae7',
        'event' => 'transaction.succeeded',
        'data' => [
            'id' => 'ZZPcHQfpqEigZhnK',
            'amount' => 50,
        ],
    ];

    Log::shouldReceive('info')->once();
    Log::shouldReceive('warning')->once();

    $response = $this->postJson('/api/givebutter/webhook', $payload);

    $response->assertSuccessful()
        ->assertJson([
            'message' => 'Transaction already exists',
            'transaction_id' => 'ZZPcHQfpqEigZhnK',
        ]);

    // Verify no duplicate was created
    $count = GivebutterTransaction::where('givebutter_id', 'ZZPcHQfpqEigZhnK')->count();
    expect($count)->toBe(1);
});

test('validates required fields', function () {
    $response = $this->postJson('/api/givebutter/webhook', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['id', 'event', 'data']);
});

test('validates data.id is required', function () {
    $payload = [
        'id' => 'b7ee9e36-cc07-479c-a34b-b271c0a6fae7',
        'event' => 'transaction.succeeded',
        'data' => [],
    ];

    $response = $this->postJson('/api/givebutter/webhook', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['data.id']);
});

test('sets status to failed for transaction.failed event', function () {
    $payload = [
        'id' => 'b7ee9e36-cc07-479c-a34b-b271c0a6fae7',
        'event' => 'transaction.failed',
        'data' => [
            'id' => 'FAILED123',
            'amount' => 50,
        ],
    ];

    Log::shouldReceive('info')->twice();

    $response = $this->postJson('/api/givebutter/webhook', $payload);

    $response->assertSuccessful();

    $transaction = GivebutterTransaction::where('givebutter_id', 'FAILED123')->first();

    expect($transaction)->not->toBeNull()
        ->and($transaction->status)->toBe('failed');
});

test('sets status to pending for unknown event types', function () {
    $payload = [
        'id' => 'b7ee9e36-cc07-479c-a34b-b271c0a6fae7',
        'event' => 'transaction.unknown',
        'data' => [
            'id' => 'UNKNOWN123',
            'amount' => 50,
        ],
    ];

    Log::shouldReceive('info')->twice();

    $response = $this->postJson('/api/givebutter/webhook', $payload);

    $response->assertSuccessful();

    $transaction = GivebutterTransaction::where('givebutter_id', 'UNKNOWN123')->first();

    expect($transaction)->not->toBeNull()
        ->and($transaction->status)->toBe('pending');
});
