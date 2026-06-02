<?php

namespace App\Observers;

use App\Models\Equipment;

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
        if ($equipment->wasChanged('total_usage_hours')) {
            $cost = $equipment->maintenance_cost;
            $roi = ($equipment->total_usage_hours / $cost) * config('app.normalization_factor');

            $recommendation = ($roi < config('app.min_roi'))
                ? "Equipment #{$equipment->id} has high maintenance costs. REVIEW"
                : "Equipment is performing efficiently. KEEP";

            $equipment->roiReports()->updateOrCreate(
                ['equipment_id' => $equipment->id],
                [
                    'roi_score' => $roi,
                    'recommendation' => $recommendation,
                ]
            );
        }
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