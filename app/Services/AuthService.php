<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Hashing\HashManager;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function register(array $data): User
    {
        /** @var User $user */
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => app(HashManager::class)->make($data['password']),
            'is_campus_member' => $data['is_campus_member'] ?? false,
            'is_active' => true,
        ]);

        if (! empty($data['role'])) {
            $user->assignRole($data['role']);
        }

        return $user;
    }

    public function login(string $email, string $password): ?Authenticatable
    {
        $credentials = ['email' => $email, 'password' => $password];

        if (! Auth::guard('web')->attempt($credentials)) {
            return null;
        }

        return Auth::guard('web')->user();
    }
}
