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
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(Request $request)
  {
    // Log the incoming request for debugging
    Log::info('Givebutter Webhook Received', [
      'headers' => $request->headers->all(),
      'payload' => $request->all(),
    ]);

    // Validate the request
    $request->validate([
      'transactions' => 'required|array',
      'transactions.*.id' => 'required|string',
      'transactions.*.amount' => 'required|numeric',
      // Add more validation as needed based on Givebutter docs
    ]);

    $transactions = $request->input('transactions');
    $created = 0;

    foreach ($transactions as $transaction) {
      // Check if transaction already exists
      $existing = GivebutterTransaction::where('givebutter_id', $transaction['id'])->first();
      if ($existing) {
        Log::warning('Duplicate Givebutter transaction received', ['id' => $transaction['id']]);
        continue;
      }

      GivebutterTransaction::create([
        'givebutter_id' => $transaction['id'],
        'payload' => $transaction,
        'status' => 'pending',
      ]);
      $created++;
    }

    return response()->json([
      'message' => 'Webhook processed successfully',
      'transactions_created' => $created,
    ], 200);
  }
}
