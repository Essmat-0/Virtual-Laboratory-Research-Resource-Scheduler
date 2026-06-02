<?php

namespace App\Http\Middleware;

use App\Models\Equipment;
use App\Models\SafetyLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSafetyLog
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $equipment = Equipment::findOrFail($request->route('id'));

        $acknowledged = SafetyLog::where('user_id', auth()->id())
            ->where('equipment_category_id', $equipment->category_id)
            ->where('acknowledgment_status', 1)
            ->exists();

        if (!$acknowledged) {
            return redirect()->route('safety.briefing', $equipment->category_id)
                ->withInput(['intended' => $request->url()]);
        }

        return $next($request);
    }
}