<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\AuditorProfile;
use App\Models\LabmProfile;
use App\Models\PiProfile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // ---------------------------------------------------------------------------
    // Role Constants
    // ---------------------------------------------------------------------------
    const ROLE_SYSTEM_ADMIN  = 1;
    const ROLE_PI            = 2;
    const ROLE_RESEARCHER    = 3;
    const ROLE_LAB_MANAGER   = 4;
    const ROLE_AUDITOR       = 5;

    const UNDERGRAD = 0;
    const GRAD = 1;
    const MASTERS = 2;
    const PHD = 3;


    // ---------------------------------------------------------------------------
    // Fillable / Hidden
    // ---------------------------------------------------------------------------
    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'role_id',
        'pis_id',
        'academicLevel',
        'budget_limit',
        'managed_Lab_Locations',
        'user_role_tier',
        'security_clearance_level',
        'is_active',
        'expiry_date',
        'clearance_level',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'expiry_date'  => 'date',
    ];


    // Foreign ID [ user->role_id ===> roles table ]

    protected $with = ['role'];

    public function isResearcher(): bool
    {
        return $this->role && $this->role->name == "Researcher";
    }
    // PROFILES 

    public function piProfile()
    {
        return $this->hasOne(PiProfile::class, 'user_id', 'id');
    }
    public function auditorProfile()
    {
        return $this->hasOne(AuditorProfile::class);
    }
    public function labmProfile()
    {
        return $this->hasOne(LabmProfile::class);
    }
    public function researcherProfile()
    {
        return $this->hasOne(ResearcherProfile::class);
    }
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // =======
    public function reservation(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }


    public function equipmentSession(): HasMany
    {
        return $this->hasMany(EquipmentSession::class);
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }





    public function assignClearance(): string
    {
        if ($this->clearance_level == $this->UNDERGRAD)
            return 'Undergraduate';
        if ($this->clearance_level == $this->GRAD)
            return 'Graduate';
        if ($this->clearance_level == $this->MASTERS)
            return 'Masters';
        if ($this->clearance_level == $this->PHD)
            return 'PhD';

        return 'unknown';
    }
}