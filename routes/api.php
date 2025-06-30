<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\YoomoneyController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\EventController;

Route::group(['prefix' => 'yoomoney'], function () {

    Route::get('redirect', [YoomoneyController::class, 'redirect']);
    Route::get('notification', [YoomoneyController::class, 'notification']);





    Route::any('payments/callback', [YoomoneyController::class, 'paymentsCallback'])->name('payment.callback');

    Route::post('/process-payment', [YoomoneyController::class, 'processPayment'])->name('payment.process');

    Route::get('/process-payment', [YoomoneyController::class, 'processPaymentGet']);

    Route::get('payment', [YoomoneyController::class, 'payment']);
    Route::get('payment/info/{paymentId}', [YoomoneyController::class, 'getPaymentInfo']);


    Route::any('payment/error', [YoomoneyController::class, 'paymentError'])->name('payment.error');

});

Route::post('feedback', [FeedbackController::class, 'feedback']);
Route::post('event', [EventController::class, 'event']);
