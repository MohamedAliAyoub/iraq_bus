<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverHistory extends Model
{
    use HasFactory;

    const PENDING = 0;
    const DRIVER_ACCEPT = 1;
    const DRIVER_CANCEL = 2;
    const SUCCESS = 3;
    const IN_PROGRESS = 4;
    const  TRANSFER = 5;


    protected $guarded = ['id'];
    protected $appends = ['total_seats'];


    public function trip(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }



    public function driver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function getTotalSeatsAttribute()
    {
        return $this->trip->fleetType->total_seats;
    }


}
