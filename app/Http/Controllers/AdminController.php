<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\RoiReport;
use App\Models\User;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function Laravel\Prompts\error;

class AdminController extends Controller
{

    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function store(Request $request)
    {

        $rules = [
            'user_name'  => 'required|string',
            'user_email' => 'required|email|unique:users,email,' . (User::where('email', $request->user_email)->first()->id ?? 'NULL'),
            'user_role'  => 'required|exists:roles,name',
            'user_pass'  =>  User::where('email', $request->user_email)->exists()
                ? 'nullable|min:6'
                : 'required|min:6',
            'expiry_date' => 'required|date',
            'system_priviliges' => 'required|string',

        ];

        if ($request->user_role === 'PI') {
            $rules['budget_limit'] = 'required|numeric';
            $rules['affiliation'] = 'required|string';
        } elseif ($request->user_role === 'Lab_Manager')
            $rules['lab_locations'] = 'required|string';
        elseif ($request->user_role === 'Auditor')
            $rules['audit_scope'] = 'required|string';
        else {
            return error('role inserted does Not exist');
        }
        $validated = $request->validate($rules);

        $user = $this->adminService->storeOrUpdateUser($validated);

        $status = $user->wasRecentlyCreated ? 'created' : 'updated';
        return redirect()->back()->with('success', "{$request->user_role} {$user->name} was successfully {$status}");
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $id = $request->input('user_id');
        if ($id == Auth::id()) {
            return back()->withErrors([
                'user_id' => 'You cannot delete your own account.'
            ]);
        }

        $this->adminService->deleteUser($request->user_id);
        return redirect()->back()->with('successDelete', "Deleted user with ID: {$id}");
    }


    public function dashboard()
    {
        $equipmentList = Equipment::where('status', 'Maintenance')->get();

        $roiReport = RoiReport::with('equipment')->get();

        $impactData = $equipmentList->map(function ($equipment) {
            $downTimeInHours = $equipment->updated_at->diffInHours(now());

            $impact = $downTimeInHours * $equipment->hourly_rate * config('app.normalization_factor');

            return [
                'equipment' => $equipment,
                'downTime'  => $downTimeInHours,
                'impact'    => $impact
            ];
        });

        return view('dashboards.admin', compact('impactData', 'roiReport'));
    }
}