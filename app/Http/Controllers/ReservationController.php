<?php

namespace App\Http\Controllers;

use App\Mail\NotifyPI;
use App\Models\Equipment;
use App\Models\Grant;
use App\Models\SafetyLog;
use App\Services\PiService;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ReservationController extends Controller
{
    protected $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    public function reservationPanel($id)
    {
        $equipment = Equipment::findOrFail($id);
        return view('equipment.book', compact('equipment'));
    }

    public function store(Request $request, $id)
    {

        $equipment = Equipment::findOrFail($id);

        $safetyLog = SafetyLog::where('user_id', auth()->id())
            ->where('equipment_category_id', $equipment->category_id)
            ->where('acknowledgment_status', 1)
            ->value('id');


        $validated = $request->validate([
            'start_time' => 'required|date|after:now',
            'end_time'   => 'required|date|after:start_time',
            'quantity'  => 'required|integer'
        ]);
        $data = [
            'user_id'      => auth()->id(),
            'equipment_id' => $equipment->id,
            'start_time'   => $validated['start_time'],
            'end_time'     => $validated['end_time'],
            'quantity' => $validated['quantity'],
            'safety_log_id' => $safetyLog,
        ];


        $reservation = $this->reservationService->makeReservation($data);
        $researcher = auth()->user()->researcherProfile;

        $piEmail = 'pi@lab.com';

        Mail::to($piEmail)->send(new NotifyPI($reservation, $researcher));


        return redirect()->route('equipment.index')->with('success', 'Reservation submitted and pending approval.');
    }
}