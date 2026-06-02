<?php

namespace App\Services;

use App\Http\Controllers\EquipmentSessionController;
use App\Models\EquipmentSession;
use App\Models\Reservation;

class GrantService
{
    // public function checkGrant(float $cost): bool
    // {

    //     $grant = auth()->user()->piProfile->grants()->first();

    //     if ($grant->expiry_date < now()) {
    //         session()->put('grant_expired', 'Grant Is Expired');
    //         return false;
    //     }else {
    //         if ($grant && $cost > $grant->balance) {
    //             return false;
    //         }
    //         $newBalance = $grant->balance - $cost;
    //         $grant->update(['balance' => $newBalance]);

    //         return true;
    //     }
    // }
}