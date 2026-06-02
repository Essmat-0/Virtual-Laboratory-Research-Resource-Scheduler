<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SafetyLog extends Model
{
    //
    protected $fillable = [
        'user_id',
        'equipment_category_id',
        'acknowledgment_status',
        'user_ip',
    ];

    public function reservation()
    {
        return $this->hasOne(Reservation::class);
    }

    public function researcher()
    {
        return $this->belongsTo(ResearcherProfile::class, 'user_id', 'user_id');
    }
}