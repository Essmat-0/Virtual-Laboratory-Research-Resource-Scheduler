<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionGrant extends Model
{
    protected $fillable = [
        'transaction_id',
        'grant_id',
        'percentage',
        'amount',
    ];


    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function grant(): BelongsTo
    {
        return $this->belongsTo(Grant::class);
    }
}