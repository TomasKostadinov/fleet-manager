<?php

namespace App\Models;

use App\Enums\FuelType;
use App\Enums\Transmission;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Vehicle extends Model implements HasMedia
{
    use InteractsWithMedia, HasUlids, SoftDeletes;

        /**
         * The attributes that are mass assignable.
         *
         * @var array<int, string>
         */
    protected $fillable = [
        'model',
        'manufacturer',
        'chassis_number',
        'seats',
        'doors',
        'kw',
        'transmission',
        'fuel_type',
        'fuel_type',
        'registration_plate',
        'registration_date',
        'tuev_valid_until',
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'registration_date' => 'datetime',
        'tuev_valid_until' => 'datetime',
        'fuel_type' => FuelType::class,
        'transmission' => Transmission::class,
    ];

    public function briefings()
    {
        return $this->hasMany(VehicleBriefing::class, 'vehicle_id');
    }

    public function allowed_drivers()
    {
        return $this->hasManyThrough(Person::class, VehicleBriefing::class);
    }

    public function getDisplayNameAttribute()
    {
        return "{$this->registration_plate} ({$this->manufacturer} {$this->model})";
    }

    public function odometerReadings()
    {
        return $this->hasMany(OdometerReading::class);
    }

    public function getLastOdometerReadingAttribute()
    {
        return $this->odometerReadings->sortByDesc('date')->first();
    }
    public function getFirstOdometerReadingAttribute()
    {
        return $this->odometerReadings->sortBy('date')->first();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('documents')->useDisk('s3');
    }
}
