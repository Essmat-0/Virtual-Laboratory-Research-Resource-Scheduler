<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'equipment_id',
        'safety_log_id',
        'grant_id',
        'start_time',
        'end_time',
        'quantity',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function safetyLog()
    {
        return $this->belongsTo(SafetyLog::class);
    }

    public function grant()
    {
        return $this->belongsTo(Grant::class);
    }

    public function session()
    {
        return $this->hasOne(EquipmentSession::class, 'user_id', 'user_id')
            ->where('equipment_id', $this->equipment_id);
    }
}