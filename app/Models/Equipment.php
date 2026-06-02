<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Config;

class Equipment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'status',
        'hourly_rate',
        'required_clearance',
        'category_id',
        'total_usage_hours',
        'location_code',
        'calibration_threshold',
        'cooldown_buffer',
        'quantity',
    ];


    protected $casts = [
        'hourly_rate' => 'float',
        'required_clearance' => 'integer',
    ];


    public function needsMaintenance(): bool
    {
        return $this->total_usage_hours >= $this->calibration_threshold;
    }

    public function equipment_session(): HasMany
    {
        return $this->hasMany(EquipmentSession::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function reservation()
    {
        return $this->hasMany(Reservation::class);
    }

    public function consumables(): BelongsToMany
    {
        return $this->belongsToMany(Consumable::class, 'equipment_consumables', 'equipment_id', 'consumable_id');
    }
    public function roiReports(): HasMany
    {
        return $this->hasMany(RoiReport::class, 'equipment_id', 'id');
    }
}