<?php

namespace App\Observers;

use App\Models\Equipment;
use App\Models\RoiReport;

class EquipmentObserver
{
    /**
     * Handle the Equipment "created" event.
     */
    public function created(Equipment $equipment): void
    {
        //
    }

    /**
     * Handle the Equipment "updated" event.
     */
    public function updated(Equipment $equipment): void
    {
        if (! $equipment->wasChanged('total_usage_hours')) {
            return;
        }

        $roi = $equipment->roiScore();

        // No maintenance cost on record yet — nothing to compute.
        if ($roi === null) {
            return;
        }

        $equipment->roiReports()->updateOrCreate(
            ['equipment_id' => $equipment->id],
            [
                'roi_score' => $roi,
                'recommendation' => RoiReport::recommend($roi),
            ]
        );
    }

    /**
     * Handle the Equipment "deleted" event.
     */
    public function deleted(Equipment $equipment): void
    {
        //
    }

    /**
     * Handle the Equipment "restored" event.
     */
    public function restored(Equipment $equipment): void
    {
        //
    }

    /**
     * Handle the Equipment "force deleted" event.
     */
    public function forceDeleted(Equipment $equipment): void
    {
        //
    }
}
