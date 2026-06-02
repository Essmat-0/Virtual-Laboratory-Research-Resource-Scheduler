<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EquipmentSession extends Model
{

    protected $fillable = [
        'user_id',
        'equipment_id',
        'start_time',
        'end_time',
        'stock_reduced'
    ];
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime'
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class, 'session_id');
    }
}