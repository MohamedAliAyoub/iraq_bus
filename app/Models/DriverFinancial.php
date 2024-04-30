<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverFinancial extends Model
{
    protected $guarded = ['id'];

    use HasFactory;

    public function driverTrip(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DriverTrips::class, 'driver_trip_id');
    }
}
