<?php

namespace App\Providers;

use App\Http\Responses\LoginResponse;
use App\Models\AuditTrails;
use App\Services\ReservationService;
use App\Services\TransactionService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);

        $this->app->singleton(ReservationService::class, function () {
            return new ReservationService();
        });

        $this->app->singleton(TransactionService::class, function () {
            return new TransactionService();
        });
    }
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Event::listen('eloquent.saved: *', function ($event, $data) {
            $model = $data[0];

            if ($model instanceof \App\Models\AuditTrails) {
                return;
            }


            $action = null;
            if ($model->wasRecentlyCreated) $action = 'Created';
            elseif ($model->getOriginal('status') !== $model->status) {
                $status = strtolower($model->status);

                if ($status === 'approved') {
                    $action = 'Approved';
                } elseif ($status === 'rejected') {
                    $action = 'Rejected';
                }
            } else {
                $action = 'Updated';
            }

            AuditTrails::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id() ?? 1,
                'action' => "{$action} " . class_basename($model) . " #{$model->id}",
                'user_ip' => request()->ip(),
            ]);
        });

        Event::listen('eloquent.deleted: *', function ($event, $data) {
            $model = $data[0];

            if ($model instanceof \App\Models\AuditTrails) {
                return;
            }


            \App\Models\AuditTrails::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id() ?? 1,
                'action'  => 'Deleted ' . get_class($model) . ' ID: ' . $model->id,
                'user_ip' => request()->ip(),
            ]);
        });
        Event::listen(Login::class, function ($event) {
            AuditTrails::create([
                'user_id' => $event->user->id ?? 1,
                'action'  => '(Logged In)',
                'user_ip' => request()->ip(),
            ]);
        });

        Event::listen(Logout::class, function ($event) {
            if ($event->user) {
                AuditTrails::create([
                    'user_id' => $event->user->id ?? 1,
                    'action'  => '(Logged Out)',
                    'user_ip' => request()->ip(),
                ]);
            }
        });


        \App\Models\Transaction::observe(\App\Observers\TransactionObserver::class);
    }
}