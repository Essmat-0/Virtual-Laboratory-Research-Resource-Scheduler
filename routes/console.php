<?php

use App\Mail\NotifyUserForCertificateExpiration;
use App\Models\Certification;
use App\Models\EquipmentSession;
use App\Models\User;
use App\Services\ConsumableService;
use App\Services\ReservationService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {

    app(ConsumableService::class)->checkStock();

    $cutoff = now()->subMinutes(15);
    $inactiveUserIds = User::where('is_active', true)->where('updated_at', '<', $cutoff)->pluck('id');

    if ($inactiveUserIds->isNotEmpty()) {
        EquipmentSession::whereIn('user_id', $inactiveUserIds)
            ->whereNull('end_time')
            ->each(function ($session) {
                if (!$session->user_id) return;


                $session->update(['end_time' => now()]);
                $durationInHours = $session->start_time->diffInMinutes(now()) / 60;
                $cost = $durationInHours * $session->equipment->hourly_rate;
                $session->equipment->update(['status' => 'Idle']);
                \App\Models\Transaction::create([
                    'session_id' => $session->id,
                    'user_id'    => $session->user_id,
                    'amount' => $cost,
                    'normalized_amount' => $cost * config('app.normalization_factor'),
                ]);
            });

        User::whereIn('id', $inactiveUserIds)->update(['is_active' => false]);
    }


    app(ReservationService::class)->convertReservationToSession();
    EquipmentSession::whereNotNull('end_time')
        ->whereDoesntHave('transaction')
        ->where('end_time', '<=', now())
        ->with('equipment')
        ->each(function ($session) {
            $durationInHours = $session->start_time->diffInMinutes($session->end_time) / 60;
            $cost = $durationInHours * $session->equipment->hourly_rate;

            \App\Models\Transaction::create([
                'session_id' => $session->id,
                'user_id'    => $session->user_id,
                'amount' => $cost,
                'normalized_amount' => $cost * config('app.normalization_factor'),
            ]);
            $eqpHours = $session->equipment->total_usage_hours;
            $session->equipment->update([
                'status'   => 'Idle',
                'quantity' => $session->equipment->quantity + 1,
                'total_usage_hours' => $eqpHours + $durationInHours,
            ]);
        });
})->everyMinute();


// check if certificaion is almost expired, then send user an email.
Schedule::call(function () {
    $certifications = Certification::all();

    foreach ($certifications as $cert) {
        if ($cert->almostExpired()) {
            Mail::to($cert->user->email)->send(new NotifyUserForCertificateExpiration($cert));
        }
    }
})->dailyAt('08:00');