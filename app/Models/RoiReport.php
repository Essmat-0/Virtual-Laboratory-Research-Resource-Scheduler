<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoiReport extends Model
{
    public const KEEP = 'Keep';

    public const REVIEW = 'Review';

    protected $fillable = [
        'equipment_id',
        'roi_score',
        'recommendation',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'equipment_id', 'id');
    }

    public static function recommend(float $roiScore): string
    {
        return $roiScore < (float) config('app.min_roi')
            ? self::REVIEW
            : self::KEEP;
    }
}
