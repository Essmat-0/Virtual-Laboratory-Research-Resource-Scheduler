<?php

namespace App\Http\Controllers;

use App\Models\EquipmentSession;
use App\Models\Reservation;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResearcherController extends Controller
{
    public function dashboard()
    {
        $id = Auth::id();
        $reservations = Reservation::where('user_id', $id)->where('start_time', '>',now())->get();
        $activeSessions = EquipmentSession::where('user_id', $id)->whereNull('end_time')->get();


        $endedSessionIds = EquipmentSession::where('user_id', $id)
            ->whereNotNull('end_time')
            ->where('end_time', '<=', now())
            ->pluck('id');

        $sessionCost = Transaction::whereIn('session_id', $endedSessionIds)->get();

        return view('dashboards.researcher', compact('reservations', 'activeSessions', 'sessionCost'));
    }
}