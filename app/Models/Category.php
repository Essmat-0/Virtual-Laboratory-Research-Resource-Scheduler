<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    protected $table = 'equipment_categories';

    public function certification(){
        return $this->hasOne(Certification::class);
    }
}