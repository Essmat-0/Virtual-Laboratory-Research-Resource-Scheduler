<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function login(array $credentials): bool
    {
        return Auth::attempt($credentials);
    }

    public function redirectPath(): string
    {
        $user = Auth::user();

        return match ($user->role_id) {
            User::ROLE_RESEARCHER => '/researcher/dashboard',
            User::ROLE_PI => '/pi/dashboard',
            User::ROLE_LAB_MANAGER => '/lab/dashboard',
            User::ROLE_SYSTEM_ADMIN => '/admin/dashboard',
            default => '/dashboard',
        };
    }

    public function logout(): void
    {
        Auth::logout();
    }
}