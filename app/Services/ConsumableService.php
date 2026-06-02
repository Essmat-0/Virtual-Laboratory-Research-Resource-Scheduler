<?php

namespace App\Services;

use App\Models\EquipmentSession;
use Illuminate\Support\Facades\DB;

class ConsumableService
{
    public function checkStock()
    {
        $sessions = EquipmentSession::with('equipment.consumables')
            ->where('end_time', '<=', now())
            ->where('stock_reduced', false)
            ->get();

        DB::transaction(function () use ($sessions) {
            foreach ($sessions as $session) {
                $equipment = $session->equipment;

                if ($equipment) {
                    foreach ($equipment->consumables as $consumable) {
                        $consumable->decrement('stock_level', 5);
                    }
                }

                $session->update(['stock_reduced' => true]);
            }
        });
    }
}