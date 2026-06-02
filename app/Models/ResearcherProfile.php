<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResearcherProfile extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'academicLevel',
        'pis_id',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function certification(): HasMany
    {
        return $this->hasMany(Certification::class, 'user_id', 'user_id');
    }
    public function isClear(): bool
    {
        return ($this->academicLevel === $this->user->assignClearance());
    }

    public function canAccess($equipment): bool
    {
        return $this->user->clearance_level >= $equipment->required_clearance;
    }

    public function piProfiles(): BelongsTo
    {
        return $this->belongsTo(PiProfile::class, 'pis_id', 'user_id');
    }
    public function SafetyLog()
    {
        return $this->hasMany(SafetyLog::class, 'user_id', 'user_id');
    }
}