<?php

namespace App\Services;

use App\Models\EquipmentSession;
use App\Models\Grant;
use App\Models\Transaction;
use App\Models\TransactionGrant;
use Illuminate\Support\Facades\DB;

class TransactionService
{
// Create a transaction from a completed session.

    public function makeNew(EquipmentSession $session, float $cost): Transaction
    {
        return Transaction::create([
            'session_id'  => $session->id,
            'user_id'     => $session->user_id,
            'amount'  => $cost,
            'normalized_amount' => $cost * config('app.normalization_factor'),
            'is_split'    => false,
        ]);
    }

    /*Allocate a transaction across multiple grants.
     * $allocations = [grant_id' => 1, 'percentage' => 60],['grant_id' => 2, 'percentage' => 40]*/
    public function allocate(Transaction $transaction, array $allocations): void
    {
        $total = array_sum(array_column($allocations, 'percentage'));
        if (round($total, 2) !== 100.00) {
            throw new \Exception("Grant allocations must sum to 100%. Got {$total}%.");
        }

        $grantIds = array_column($allocations, 'grant_id');
        if (count($grantIds) !== count(array_unique($grantIds))) {
            throw new \Exception("Duplicate grants are not allowed in a single allocation.");
        }


        foreach ($allocations as $allocation) {
            $grant = Grant::findOrFail($allocation['grant_id']);
            $amount = round(($allocation['percentage'] / 100) * $transaction->amount, 2);
            if ($grant->expiry_date < now()) {
                throw new \Exception("Grant '{$grant->name}' has expired and cannot be used.");
            }

            if ($grant->balance < $amount) {
                throw new \Exception("Grant '{$grant->name}' has insufficient balance. 
                    Required: {$amount}, Available: {$grant->balance}.");
            }
        }


        DB::transaction(function () use ($transaction, $allocations) {
            // Wipe any previous allocation attempt on this transaction
            TransactionGrant::where('transaction_id', $transaction->id)->delete();

            foreach ($allocations as $allocation) {
                $amount = round(($allocation['percentage'] / 100) * $transaction->amount, 2);
                $grant  = Grant::findOrFail($allocation['grant_id']);

                TransactionGrant::create([
                    'transaction_id' => $transaction->id,
                    'grant_id'       => $allocation['grant_id'],
                    'percentage'     => $allocation['percentage'],
                    'amount'         => $amount,
                ]);


                $grant->decrement('balance', $amount);
            }

            $transaction->update([
                'is_split' => count($allocations) > 1,
            ]);
        });
    }

    
    //Returns all transactions linked to a grant, with the normalization factor applied.
    
    public function monthlyGrantSummary(int $grantId, int $month, int $year): array
    {


        $rows = TransactionGrant::where('grant_id', $grantId)
            ->whereHas('transaction', function ($q) use ($month, $year) {
                $q->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year);
            })
            ->with(['transaction.session.equipment', 'grant'])
            ->get();

        $subtotal = $rows->sum('amount');

        return [
            'rows'                 => $rows,
            'subtotal'             => $subtotal,
            'normalized_total'     => round($subtotal * config('app.normalization_factor'), 2),
            'grant_id'             => $grantId,
            'month'                => $month,
            'year'                 => $year,
        ];
    }
}