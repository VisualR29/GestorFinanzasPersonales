<?php

use App\Http\Controllers\Api\AccountApiController;
use App\Http\Controllers\Api\AuthTokenController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\TransactionApiController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthTokenController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', [AuthTokenController::class, 'logout']);
    Route::apiResource('accounts', AccountApiController::class);
    Route::apiResource('categories', CategoryApiController::class);
    Route::apiResource('transactions', TransactionApiController::class);
});
