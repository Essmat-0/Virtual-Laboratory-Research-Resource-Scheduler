<?php

namespace App\Observers;

use App\Models\Equipment;
use App\Models\EquipmentSession;

class EquipmentSessionObserver
{
    /**
     * Handle the EquipmentSession "created" event.
     */
    public function created(EquipmentSession $equipmentSession): void
    {
        $this->updateUsageHoursInEquipment($equipmentSession->equipment_id);
    }

    /**
     * Handle the EquipmentSession "updated" event.
     */
    public function updated(EquipmentSession $equipmentSession): void
    {
        $this->updateUsageHoursInEquipment($equipmentSession->equipment_id);
        //
    }

    /**
     * Handle the EquipmentSession "deleted" event.
     */
    public function deleted(EquipmentSession $equipmentSession): void
    {
        $this->updateUsageHoursInEquipment($equipmentSession->equipment_id);
        //
    }

    /**
     * Handle the EquipmentSession "restored" event.
     */
    public function restored(EquipmentSession $equipmentSession): void
    {
        //
    }

    /**
     * Handle the EquipmentSession "force deleted" event.
     */
    public function forceDeleted(EquipmentSession $equipmentSession): void
    {
        //
    }


    public function updateUsageHoursInEquipment($equipmentId)
    {
        $equipment = Equipment::find($equipmentId);

        $total = EquipmentSession::where('equipment_id', $equipmentId)->selectRaw('SUM(TIMESTAMPDIFF(MINUTE, start_time, end_time)) / 60 as total_hours')
            ->value('total_hours') ?? 0;

        $equipment->update([
            'total_usage_hours' => $total,
        ]);
    }
}