<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\UserFcmToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function registerFcmToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'device_type' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data token FCM tidak valid.',
                'data' => null,
                'meta' => null,
            ], 422);
        }

        $validated = $validator->validated();

        $fcmToken = UserFcmToken::query()->updateOrCreate(
            ['user_id' => $request->user()->id, 'token' => $validated['token']],
            [
                'device_type' => $validated['device_type'] ?? null,
                'is_active' => true,
                'last_used_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Token FCM terdaftar.',
            'data' => ['token' => $fcmToken->token],
            'meta' => null,
        ]);
    }
}
