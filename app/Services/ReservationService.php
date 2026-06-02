<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\EquipmentSession;
use App\Models\Reservation;
use Carbon\Carbon;

class ReservationService
{


    public function makeReservation(array $data): Reservation
    {

        $reservation = Reservation::create([
            'user_id'      => $data['user_id'],
            'equipment_id' => $data['equipment_id'],
            'safety_log_id' => $data['safety_log_id'],
            'start_time'   => $data['start_time'],
            'end_time'     => $data['end_time'],
            'quantity'     => $data['quantity'],
            'status'       => 'Pending',
        ]);

        $equipment = Equipment::findOrFail($data['equipment_id']);
        $newQty = $equipment->quantity - $data['quantity'];
        $equipment->update([
            'quantity' => $newQty
        ]);
        return $reservation;
    }


    public function calculateCost(Reservation $reservation): float
    {
        $start = Carbon::parse($reservation->start_time);
        $end = Carbon::parse($reservation->end_time);

        $hours = $start->diffInMinutes($end) / 60;

        return $hours * $reservation->equipment->hourly_rate;
    }

    public function convertReservationToSession()
    {
        Reservation::where('status', 'Approved')
            ->where('start_time', '<=', now())
            ->each(function ($reservation) {

                $alreadyExists = EquipmentSession::where('user_id', $reservation->user_id)
                    ->where('equipment_id', $reservation->equipment_id)
                    ->whereRaw('DATE_FORMAT(start_time, "%Y-%m-%d %H:%i") = ?', [
                        $reservation->start_time->format('Y-m-d H:i')
                    ])
                    ->exists();

                if (!$alreadyExists) {
                    EquipmentSession::create([
                        'user_id'      => $reservation->user_id,
                        'equipment_id' => $reservation->equipment_id,
                        'start_time'   => $reservation->start_time,
                        'end_time'     => $reservation->end_time,
                    ]);
                }
            });
    }
}