<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditTrails extends Model
{
    public const UPDATED_AT = null; //theres no updated_at coluimn in audit table
    protected $fillable = [
        'user_id',
        'action',
        'user_ip',
    ];
}