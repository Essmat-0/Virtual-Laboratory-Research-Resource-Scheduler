<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certification extends Model
{
    protected $fillable = [
        'user_id',
        'equipment_category_id',
        'expiry_date',
    ];

    protected $casts = [
        'expiry_date' => 'datetime',
    ];

    public function researcherProfile(): BelongsTo
    {
        return $this->belongsTo(ResearcherProfile::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function almostExpired()
    {
        $today = now()->startOfDay();
        $expiry = $this->expiry_date->startOfDay();

        return $expiry->isFuture() && $today->diffInDays($expiry) <= 30;
    }
}