<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BookingControllerV2 as BookingController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\QRCodeController;
use App\Http\Controllers\Api\V1\VenueController;
use App\Http\Controllers\Api\V1\VoucherController;
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

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/bookings', [BookingController::class, 'index']);
        Route::post('/bookings', [BookingController::class, 'store']);
        Route::get('/bookings/{booking}', [BookingController::class, 'show']);
        Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancel']);
        Route::patch('/bookings/{booking}/check-in', [BookingController::class, 'checkIn']);
        Route::get('/bookings/{booking}/qr', [QRCodeController::class, 'show']);

        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::post('/profile/fcm-token', [ProfileController::class, 'registerFcmToken']);

        Route::post('/vouchers/validate', [VoucherController::class, 'validate']);
        Route::get('/vouchers/active', [VoucherController::class, 'activePromos']);
    });

    Route::middleware(['auth:sanctum', 'role:koorlap,admin'])->group(function () {
        Route::post('/qr/validate', [QRCodeController::class, 'validateQr']);
        Route::get('/scan-qr', function () {
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

    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::get('/admin-only', function () {
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
    });

    Route::get('/debug/slot-lock-status/{slotId}/{date}', [BookingController::class, 'slotLockStatus']);
});

// Midtrans webhook (no auth middleware)
Route::post('/webhook/midtrans', [PaymentController::class, 'webhook']);

// Payment endpoints (authenticated, under /api)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/bookings/{id}/payment', [PaymentController::class, 'initiate']);
    Route::get('/bookings/{id}/payment/status', [PaymentController::class, 'status']);
});