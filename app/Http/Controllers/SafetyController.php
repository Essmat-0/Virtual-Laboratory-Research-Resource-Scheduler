<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SafetyLog;
use Illuminate\Http\Request;

class SafetyController extends Controller
{

    public function show(Category $category)
    {
        return view('safetyBriefing', compact('category'));
    }

    public function acknowledge(Request $request, Category $category)
    {
        SafetyLog::updateOrCreate(
            [
                'user_id'     => auth()->id(),
                'equipment_category_id' => $category->id,
            ],
            [
                'acknowledgment_status'    => true,
            ]
        );

        $intended = $request->input('intended');
        return $intended ? redirect($intended) : redirect()->route('equipment.index');
    }
}