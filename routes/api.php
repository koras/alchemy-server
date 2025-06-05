<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\YoomoneyController;

Route::group(['prefix' => 'yoomoney'], function () {

    Route::get('redirect', [YoomoneyController::class, 'redirect']);
    Route::get('notification', [YoomoneyController::class, 'notification']);
});
