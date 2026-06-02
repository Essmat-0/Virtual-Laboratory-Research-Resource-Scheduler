<?php

namespace App\Observers;

use App\Models\AuditTrails;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        $this->log($transaction, "New Transaction initiated #{$transaction->id}. Amount: {$transaction->amount} | Normalized Amount: {$transaction->normalized_amount}");
        
            
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        if ($transaction->isDirty('amount')) {
            $this->log($transaction, "CRITICAL: Amount altered from {$transaction->getOriginal('amount')} to {$transaction->amount}");
        }
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(Transaction $transaction): void
    {
        //
    }

    protected function log(Transaction $model, $message)
    {
        AuditTrails::create([
            'user_id' => Auth::id() ?? 1,
            'action'  => "BILLING: " . $message . " (ID: {$model->id})",
            'user_ip' => request()->ip(),
        ]);
    }
}