<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService)
    {
    }

    public function register(Request $request): JsonResponse
    {
        $attributes = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', 'in:waban,umum,koorlap,admin'],
            'phone' => ['sometimes', 'nullable', 'string'],
            'is_campus_member' => ['sometimes', 'boolean'],
        ]);

        $user = $this->authService->register($attributes);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->getRoleNames()->first(),
            ],
            'meta' => [
                'timestamp' => now()->toIso8601String(),
                'version' => '1.0.0',
            ],
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $attributes = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! $user = $this->authService->login($attributes['email'], $attributes['password'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
                'data' => null,
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'version' => '1.0.0',
                ],
            ], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data' => ['token' => $token],
            'meta' => [
                'timestamp' => now()->toIso8601String(),
                'version' => '1.0.0',
            ],
        ]);
    }
}
