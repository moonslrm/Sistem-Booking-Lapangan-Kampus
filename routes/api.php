<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
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
});