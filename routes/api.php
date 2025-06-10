<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\YoomoneyController;

Route::group(['prefix' => 'yoomoney'], function () {

    Route::get('redirect', [YoomoneyController::class, 'redirect']);
    Route::get('notification', [YoomoneyController::class, 'notification']);



    Route::post('payments/callback', [YoomoneyController::class, 'paymentsCallback'])->name('payment.callback');

    Route::post('/process-payment', [YoomoneyController::class, 'processPayment'])->name('payment.process');

    Route::get('payment', [YoomoneyController::class, 'payment']);
    Route::get('payment/info/{paymentId}', [YoomoneyController::class, 'getPaymentInfo']);



});
