<?php

use App\Http\Controllers\Api\CalculationController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:api')->group(function () {
    Route::apiResource('calculations', CalculationController::class)->only([
        'index',
        'store',
        'destroy',
    ]);

    Route::delete('calculations/all', [CalculationController::class, 'destroyAll']);
});
