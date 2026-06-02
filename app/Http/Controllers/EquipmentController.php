<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $userCertificates = $user?->researcherProfile?->certification ?? collect();
        $equipment = Equipment::with('category')->paginate(12);
        return view('welcome', compact('equipment', 'userCertificates'));
    }

    public function show($id)
    {
        $equipment = Equipment::findOrFail($id);
        return view('equipment.show', compact('equipment'));
    }
    public function book($id)
    {
        $equipment = Equipment::findOrFail($id);
        return view('equipment.book', compact('equipment'));
    }

    public function specs(Equipment $equipment)
    {
        return view('equipment.specs', compact('equipment'));
    }
}