<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\EquipmentSession;
use App\Models\Reservation;

class LabMService
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}


    public function addNewEquipment(array $data)
    {
        $values = [
            'status' => $data['equipment_status'],
            'hourly_rate' => $data['hourly_rate'],
            'required_clearance'     => $data['required_clearance'],
            'category_id' => $data['category_id'],
            'location_code' => $data['location_code'],
            'total_usage_hours' => Equipment::totalUsageHours(),
            'calibration_threshold' => $data['calibration_threshold'],
            'maintenance_cost' => $data['maintenance_cost'],
            'cooldown_buffer' => $data['cooldown_buffer'],
            'quantity' => $data['quantity'],
        ];

        return  Equipment::updateOrCreate(['name' => $data['equipment_name']], $values);
    }

    public function deleteEquipment($id)
    {
        return  Equipment::where('id', $id)->firstOrFail()->delete();
    }


    public function setMaintenance(Equipment $equipment)
    {
        $equipment->update(['status' => 'Maintenance']);
        return redirect()->route('Lab_Manager.dashboard', ['tab' => 'inventory'])->with('success', '...');
    }
    public function setIdle(Equipment $equipment)
    {
        $equipment->update(['status' => 'Idle']);
        return redirect()->route('Lab_Manager.dashboard', ['tab' => 'inventory'])->with('success', '...');
    }

    public function emergencyLockout(): bool
    {
        Equipment::all()->each(function ($equipment) {
            $equipment->update(['status' => 'Maintenance']);
        });
        EquipmentSession::whereNull('end_time')->update([
            'end_time' => now()
        ]);
        Reservation::where('status', '!=', 'Cancelled')
            ->where('start_time', '>', now())
            ->update(['status' => 'Cancelled']);
        return true;
    }
}