<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\User;
use App\Services\LabMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LabManagerController extends Controller
{
    protected $labService;

    public function __construct(LabMService $labService)
    {
        $this->labService = $labService;
    }

    public function dashboard()
    {
        $equipments = Equipment::all();

        return view('dashboards.labmanager', compact('equipments'));
    }

    public function store(Request $request)
    {
        $rules = [
            'equipment_name'  => 'required|string',
            'equipment_status' => 'required|string',
            'hourly_rate'  => 'required|numeric',
            'required_clearance' => 'required|integer',
            'category_id' => 'required|integer',
            'location_code' => 'required|string',
            'calibration_threshold' => 'required|numeric',
            'maintenance_cost' => 'required|numeric',
            'cooldown_buffer' => 'required|integer',
            'quantity' =>   'required|integer',
        ];
        $validated = $request->validate($rules);

        $equip = $this->labService->addNewEquipment($validated);
        $status = $equip->wasRecentlyCreated ? 'created' : 'updated';

        return redirect()->back()->with('success', "Equipment {$equip->name} was successfully {$status}.");
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'equipment_id' => 'required|integer|exists:equipment,id',
        ]);
        $id = $request->input('equipment_id');
        $this->labService->deleteEquipment($request->equipment_id);
        return "Deleted Equipment with ID: " . $id;
    }

    public function emergencyMaintenance()
    {
        if ($this->labService->emergencyLockout())
            return redirect()->back()->with('success', "emergency maintenance lockout was succeffully applied for all equipments.");
    }
}