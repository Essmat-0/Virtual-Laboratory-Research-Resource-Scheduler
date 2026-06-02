<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grant extends Model
{
    protected $fillable = [
        'name',
        'pi_id',
        'balance',
    ];

    public function piProfiles(): BelongsTo
    {
        return  $this->belongsTo(PiProfile::class);
    }
}