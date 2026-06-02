<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentSession;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HeatmapController extends Controller
{
    
    public function utilization(Request $request)
    {
        $days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
        $hours = range(0,23);

        // initialize count grid
        $grid = [];
        for ($d = 0; $d < 7; $d++) {
            $grid[$d] = array_fill(0, 24, 0);
        }

        // gather sessions (all sessions; controller could limit by date if desired)
        $sessions = EquipmentSession::whereNotNull('start_time')->whereNotNull('end_time')->get();

        foreach ($sessions as $s) {
            $start = $s->start_time->copy()->minute(0)->second(0);
            $end = $s->end_time->copy()->minute(0)->second(0);

            // If end equals start, count as one hour slot
            if ($end->lte($start)) {
                continue;
            }

            // iterate each hour slot covered by the session
            $t = $start->copy();
            while ($t->lt($end)) {
                $dow = $t->dayOfWeekIso - 1; 
                $h = $t->hour;
                $grid[$dow][$h] = ($grid[$dow][$h] ?? 0) + 1;
                $t->addHour();
            }
        }

        $equipmentCount = Equipment::count() ?: 1;

        // compute utilization percent per cell
        $percents = [];
        for ($d = 0; $d < 7; $d++) {
            $percents[$d] = [];
            for ($h = 0; $h < 24; $h++) {
                $percents[$d][$h] = min(100, ($grid[$d][$h] / $equipmentCount) * 100);
            }
        }

        return response()->json([
            'days' => $days,
            'hours' => $hours,
            'counts' => $grid,
            'percents' => $percents,
            'equipment_count' => $equipmentCount,
        ]);
    }
}