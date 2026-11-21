<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    /**
     * Endpoint: POST https://vanhung.xyz/api/webhook/bank
     * Header: Authorization: Apikey <BANK_WEBHOOK_SECRET>
     * Tương thích định dạng JSON của SePay / Casso / Web2M
     */
    public function handleBankWebhook(Request $request): JsonResponse
    {
        $secret = (string) env('BANK_WEBHOOK_SECRET', '');
        $authHeader = (string) $request->header('Authorization', '');
        if ($secret === '' || !hash_equals($secret, trim(str_ireplace(['Apikey ', 'Bearer '], '', $authHeader)))) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $txId = (string) ($request->input('id') ?? $request->input('referenceCode') ?? '');
        $amount = (int) ($request->input('transferAmount') ?? $request->input('amount') ?? 0);
        $content = (string) ($request->input('content') ?? $request->input('description') ?? '');
        $transferType = strtolower((string) ($request->input('transferType') ?? 'in'));

        if ($transferType !== 'in' || $amount < 10000 || $txId === '') {
            return response()->json(['success' => true, 'message' => 'Ignored']);
        }

        if (!preg_match('/MMO(\d+)/i', $content, $matches)) {
            return response()->json(['success' => true, 'message' => 'No matching MMO user syntax']);
        }

        $userId = (int) $matches[1];

        DB::transaction(function () use ($txId, $amount, $content, $userId) {
            if (DB::getSchemaBuilder()->hasTable('bank_deposits')) {
                if (DB::table('bank_deposits')->where('transaction_id', $txId)->exists()) {
                    return;
                }
            }

            $user = User::lockForUpdate()->find($userId);
            if (!$user) {
                return;
            }

            $balanceBefore = $user->balance;
            $user->increment('balance', $amount);

            if (DB::getSchemaBuilder()->hasTable('bank_deposits')) {
                DB::table('bank_deposits')->insert([
                    'transaction_id' => $txId,
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'content' => $content,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if (DB::getSchemaBuilder()->hasTable('money_transactions')) {
                DB::table('money_transactions')->insert([
                    'user_id' => $user->id,
                    'type' => 'deposit',
                    'amount' => $amount,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceBefore + $amount,
                    'description' => "Auto Bank Webhook (#{$txId}): {$content}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            Log::info('Auto bank deposit credited', ['user_id' => $user->id, 'amount' => $amount, 'tx_id' => $txId]);
        });

        return response()->json(['success' => true]);
    }
}
