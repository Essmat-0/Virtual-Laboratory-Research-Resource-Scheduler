<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\EquipmentSession;

class EquipmentService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function canEquipmentBeUsed($equipmentId)
    {
        $equipment = Equipment::findOrFail($equipmentId);

        $lastSession = EquipmentSession::where('equipment_id', $equipmentId)
            ->latest('end_time')
            ->first();

        if (!$lastSession) return true;

        $coolDownExpiresAt = $lastSession->end_time->addMinutes($equipment->cooldown_buffer);

        return now()->greaterThan($coolDownExpiresAt);
    }

}