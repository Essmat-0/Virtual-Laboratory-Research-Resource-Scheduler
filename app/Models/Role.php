<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id');
    }
}