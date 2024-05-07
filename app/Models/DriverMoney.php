<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverMoney extends Model
{
    CONST SUSPENDED_BALANCE= 0 ;
    CONST CURRENT_BALANCE = 1 ;
    use HasFactory;
    protected $guarded = ["id"];

    public function driverTrip(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DriverTrips::class, 'driver_trip_id');
    }

    public function driver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
