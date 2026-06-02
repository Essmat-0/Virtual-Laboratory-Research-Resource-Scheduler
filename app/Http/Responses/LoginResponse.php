<?php

namespace App\Http\Responses;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        $user = auth()->user();
        return match ($user->role_id) {
            User::ROLE_SYSTEM_ADMIN => redirect()->route('Admin.dashboard'),
            User::ROLE_LAB_MANAGER => redirect()->route('Lab_Manager.dashboard'),
            User::ROLE_PI => redirect()->route('PI.dashboard'),
            User::ROLE_RESEARCHER => redirect()->route('equipment.index'),
            User::ROLE_AUDITOR => redirect()->route('Auditor.dashboard'),
            default => redirect('/'),
        };
    }
}