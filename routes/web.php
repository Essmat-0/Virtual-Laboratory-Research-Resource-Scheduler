<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuditorController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\EquipmentSessionController;
use App\Http\Controllers\HeatmapController;
use App\Http\Controllers\LabManagerController;
use App\Http\Controllers\PiController;
use App\Http\Controllers\ResearcherController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\SafetyController;
use App\Livewire\Actions\Logout;
use App\Mail\NotifyPI;
use App\Mail\NotifyUserForCertificateExpiration;
use App\Models\Certification;
use App\Services\LabMService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;


// welcome page

Route::middleware(['auth'])->group(function () {
    Route::get('/', [EquipmentController::class, 'index'])->name('equipment.index');
    //Dashboards
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->middleware('role:Admin')->name('Admin.dashboard');
    Route::get('/labmanager/dashboard', [LabManagerController::class, 'dashboard'])->middleware('role:Lab_Manager')->name('Lab_Manager.dashboard');
    Route::get('/pi/dashboard', [PiController::class, 'dashboard'])->middleware('role:PI')->name('PI.dashboard');
    Route::get('/auditor/dashboard', [AuditorController::class, 'dashboard'])->name('Auditor.dashboard')->middleware('role:Auditor');
    Route::get('/researcher/dashboard', [ResearcherController::class, 'dashboard'])->name('Researcher.dashboard')->middleware('role:Researcher');

    //Functions
    Route::post('/logout', [Logout::class, '__invoke'])->name('logout');
    Route::get('/equipment/{id}', [EquipmentController::class, 'show'])->name('equipment.show');
});

//==========================================

Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::post('/adminAddUser', [AdminController::class, 'store'])->name('admin.users.store');
    Route::delete('/adminDeleteUser', [AdminController::class, 'destroy'])->name('admin.users.destroy');
});

//==========================================

Route::middleware(['auth', 'role:PI'])->group(function () {
    Route::post('/piAddResearcher', [PiController::class, 'store'])->name('pi.researcher.store');
    Route::patch('/pi/reservations/{reservation}/approve', [PiController::class, 'approve'])->name('pi.reservation.approve');
    Route::patch('/pi/reservations/{reservation}/reject', [PiController::class, 'reject'])->name('pi.reservation.reject');
    Route::post('/pi/publications', [PiController::class, 'storePublication'])->name('pi.publication.store');
    Route::post('/pi/transactions/{transaction}/allocate', [PiController::class, 'allocateTransaction'])->name('pi.transaction.allocate');
    Route::post('pi/invoice/generate', [PiController::class, 'generateInvoice'])
        ->name('pi.invoice.generate');
});

//==========================================

route::middleware(['auth', 'role:Lab_Manager'])->group(function () {
    Route::post('/LabmStoreEquipment', [LabManagerController::class, 'store'])->name('LabM.equipment.store');
    Route::delete('/LabmDeleteEquipment', [LabManagerController::class, 'destroy'])->name('LabM.equipment.destroy');
    Route::patch('/labmanager/equipment/{equipment}/maintenance', [LabMService::class, 'setMaintenance'])->name('LabM.equipment.setMaintenance');
    Route::patch('/labmanager/equipment/{equipment}/Idle', [LabMService::class, 'setIdle'])->name('LabM.equipment.setIdle');
    Route::get('/labmanager/heatmap', [HeatmapController::class, 'utilization'])->name('LabM.heatmap');
    Route::post('/labmanager/emergency', [LabManagerController::class, 'emergencyMaintenance'])->name('emergency.maintenance');
});

//==========================================
Route::middleware(['auth', 'role:Auditor'])->group(function () {
    Route::get('pdf-export', [AuditorController::class, 'exportPdf'])->name('export-pdf');
});

//==========================================

Route::middleware(['auth', 'role:Researcher'])->group(function () {
    Route::get('/equipment/{id}/book',  [ReservationController::class, 'reservationPanel'])->middleware('safety.acknowledged')->name('equipment.book');

    Route::post('/equipment/{id}/book', [ReservationController::class, 'store'])->name('equipment.book.store');
    Route::post('/equipment/{equipment}/session/start', [EquipmentSessionController::class, 'storeSessionForStartNow'])->name('equipment.session.start');
    Route::get('equipment/{equipment}/specs', [EquipmentController::class, 'specs'])->name('equipment.specs');


    Route::patch('/researcher/dashboard/sessions/{id}/checkout', [EquipmentSessionController::class, 'endSessionForCheckout'])->name('researcher.session.checkout');

    Route::get('/safety/{category}',              [SafetyController::class, 'show'])->name('safety.briefing');
    Route::post('/safety/{category}/acknowledge', [SafetyController::class, 'acknowledge'])->name('safety.acknowledge');




    // EMAILS 
    Route::get('/send-email', function () {
        $recipient = 'Pi@lab.com';
        $name = 'Pi 1';

        Mail::to($recipient)->send(new NotifyPI($name));

        return "Email sent successfully!";
    });
    Route::get('/test-expiry-mail', function () {
        $certifications = Certification::all();
        $count = 0;
        foreach ($certifications as $cert) {
            if ($cert->almostExpired()) {
                Mail::to($cert->user->email)->send(new NotifyUserForCertificateExpiration($cert));
                $count++;
            }
        }
        return "Check completed! Sent {$count} emails to the log file.";
    });
});

//==========================================
// Ping For Heartbeat,,,, checks if user is active -> ping the user to update activity
Route::middleware('auth')->post('/heartbeat', function () {
    auth()->user()->touch();
    return response()->json(['status' => 'ok']);
})->name('heartbeat');