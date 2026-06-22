<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\VenueController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('throttle:api')->group(function () {
    Route::get('/health', function () {
        return response()->json([
            'success' => true,
            'message' => 'CSBS API v1 is running.',
            'data' => null,
            'meta' => [
                'timestamp' => now()->toIso8601String(),
                'version' => '1.0.0',
            ],
        ]);
    });

    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/venues', [VenueController::class, 'index']);
    Route::get('/venues/{id}', [VenueController::class, 'show']);
    Route::get('/venues/{id}/slots', [VenueController::class, 'slots']);
    Route::get('/venues/{id}/reviews', [VenueController::class, 'reviews']);

    Route::middleware(['auth:sanctum', 'role:admin'])->get('/admin-only', function () {
        return response()->json([
            'success' => true,
            'message' => 'Admin access granted.',
            'data' => null,
            'meta' => [
                'timestamp' => now()->toIso8601String(),
                'version' => '1.0.0',
            ],
        ]);
    });

    Route::middleware(['auth:sanctum', 'role:koorlap,admin'])->get('/scan-qr', function () {
        return response()->json([
            'success' => true,
            'message' => 'QR scan access granted.',
            'data' => null,
            'meta' => [
                'timestamp' => now()->toIso8601String(),
                'version' => '1.0.0',
            ],
        ]);
    });
});