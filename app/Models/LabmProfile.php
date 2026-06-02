<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabmProfile extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'managed_Lab_Locations'
    ];
}