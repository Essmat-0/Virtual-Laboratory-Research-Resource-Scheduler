<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AuditorController extends Controller
{

    public function dashboard()
    {
        $equipment = Equipment::orderBy('hourly_rate', 'desc')->get();

        //join users just for the name
        $auditLogs = DB::table('audit_trails')
            ->join('users', 'audit_trails.user_id', '=', 'users.id')
            ->select('audit_trails.*', 'users.name as user_name')
            ->orderByDesc('audit_trails.created_at')
            ->limit(50)
            ->get();

        return view('dashboards.auditor', compact('equipment', 'auditLogs'));
    }
    public function exportPDF()
    {
        $count = (int)0;
        $auditLogs = DB::table('audit_trails')
            ->join('users', 'audit_trails.user_id', '=', 'users.id')
            ->select('audit_trails.*', 'users.name as user_name')
            ->orderByDesc('audit_trails.created_at')
            ->limit(50)
            ->get();
        $pdf = Pdf::loadview('dashboards.auditLogs', compact('auditLogs'));
        $filename = 'AuditLogs_' . (++$count) . '.pdf';
        return $pdf->download($filename);
        //   return view('dashboards.auditLogs', compact('auditLogs'));
    }
}