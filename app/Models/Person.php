<?php

namespace App\Models;

use App\Enums\FuelType;
use App\Enums\Transmission;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Person extends Model
{
    use HasUlids, SoftDeletes, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'license_issue_date',
        'last_license_check_date',
        'notes',
        'blue_light_rights',
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'license_issue_date' => 'datetime',
        'last_license_check_date' => 'datetime',
        'blue_light_rights' => 'boolean',
    ];

    public function briefings()
    {
        return $this->hasMany(VehicleBriefing::class, 'person_id');
    }

    public function has_briefed()
    {
        return $this->hasMany(VehicleBriefing::class, 'issuer_id');
    }

    public function allowed_vehicles()
    {
        return $this->hasManyThrough(Vehicle::class, VehicleBriefing::class);
    }

    // combine first and last name as full name
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
