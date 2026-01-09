<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CommentCountController;
use App\Http\Controllers\StripeWebhookController;

Route::get('/comments/count', CommentCountController::class);
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);
