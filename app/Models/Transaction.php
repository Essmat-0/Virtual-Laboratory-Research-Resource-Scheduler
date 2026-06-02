<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model // AKA BillingRecord Class
{

    protected $fillable = [
        'session_id',
        'user_id',
        'amount',
        'normalized_amount',
        'is_split',
    ];


    public function equipmentSession(): BelongsTo
    {
        return $this->belongsTo(EquipmentSession::class, 'session_id');
    }
    public function transactionGrants(): HasMany
    {
        return $this->hasMany(TransactionGrant::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}