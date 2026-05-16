<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Property extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'owner_id', 'location', 'province_id', 'city_id', 'property_type_id', 'description',
        'rooms', 'bathrooms', 'has_garage', 'has_patio', 'has_balcony', 'pets_allowed', 'square_meters'
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function type()
    {
        return $this->belongsTo(PropertyType::class, 'property_type_id');
    }

    public function leases()
    {
        return $this->hasMany(Lease::class);
    }

    public function activeLease()
    {
        return $this->hasOne(Lease::class)->where('is_active', true);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function documents()
    {
        return $this->hasMany(PropertyDocument::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }
}
