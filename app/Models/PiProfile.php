<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PiProfile extends Model
{
    protected $primaryKey = 'user_id';
    public $incrementing = false;

    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'budget_limit',
        'affiliation'
    ];

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function grants(): HasMany
    {
        return $this->hasMany(Grant::class, 'pi_id', 'user_id');
    }

    public function researchers(): HasMany
    {
        return $this->hasMany(ResearcherProfile::class, 'pis_id', 'user_id');
    }
}