<?php

namespace App\Services;

use App\Http\Controllers\EquipmentSessionController;
use App\Models\Equipment;
use App\Models\Grant;
use App\Models\PublicationLink;
use App\Models\Reservation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use phpDocumentor\Reflection\Types\Boolean;

class PiService
{

    public function StoreorUpdateResearcher(array $data)
    {
        return DB::transaction(function () use ($data) {


            $role = Role::where('name', 'Researcher')->firstOrFail();

            $userValues = [
                'name'  => $data['user_name'],
                'password' => Hash::make($data['user_pass']),
                'role_id' => $role->id,
                'expiry_date' => $data['expiry_date'],
                'is_active' => true,
                'clearance_level' => $data['clearance_level'],
            ];
            if (!empty($data['user_pass'])) {
                $userValues['password'] = Hash::make($data['user_pass']);
            }

            $user = User::updateOrCreate(['email' => $data['user_email']], $userValues);

            $user->researcherProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'academic_leve' => $data['academic_level'],
                    'pis_id' => Auth::id(),
                ]
            );
            return $user;
        });
    }




    public function notifyPI(Reservation $reservation): void {}

    public function approve(Reservation $reservation, float $cost): bool
    {
        $pi = auth()->user()->piProfile;

        if ($cost > $pi->budget_limit) {
            throw new \Exception("Budget exceeded");
        }

        $hasValidGrant = $pi->grants()->where('expiry_date', '>', now())->exists();

        if (!$hasValidGrant) {
            throw new \Exception("No active grants available. All grants have expired.");
        }

        $reservation->update(['status' => 'Approved']);
        return true;
    }

    public function reject(Reservation $reservation): void
    {
        $reservation->update(['status' => 'Rejected']);
        $equipment = Equipment::findOrFail($reservation->equipment_id);
        $quantity = $equipment->quantity + $reservation->quantity;
        $equipment->update([
            'quantity' => $quantity,
        ]);
    }

    public function usedEquipments($pi)
    {
        return Equipment::whereHas('reservation', function ($q) use ($pi) {
            $q->whereIn(
                'user_id',
                $pi->researchers()->pluck('user_id')
            )
                ->where('status', 'approved');
        })->get();
    }

    public function publicationLinks()
    {
        return PublicationLink::where('pi_id', auth()->user()->piProfile->user_id)
            ->with('equipment')
            ->latest()
            ->get();
    }

    public function storePublication(array $data)
    {
        $publicationLink = PublicationLink::create([
            'equipment_id' => $data['equipment_id'],
            'pi_id' => $data['pi_id'],
            'doi' => $data['doi'],
        ]);

        return $publicationLink;
    }
}