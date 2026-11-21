<?php

use App\Http\Controllers\Api\PaymentWebhookController;
use Illuminate\Support\Facades\Route;

// Webhook Auto Bank (SePay / Casso / Web2M) -> https://vanhung.xyz/api/webhook/bank
Route::post('/webhook/bank', [PaymentWebhookController::class, 'handleBankWebhook']);
