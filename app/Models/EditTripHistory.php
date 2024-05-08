<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EditTripHistory extends Model
{
    use HasFactory;

    const PENDING = 0; // new record
    const SUCCESS = 2; // driver work in this record and driver has one record with status success
    const OLD = 3; //it was success in the past
    const REJECTED = 4; // after pending admin reject it

    protected $guarded = ['id'];
    protected $casts = [
        'day_off' => 'array'
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(VehicleRoute::class, 'route_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }
}
