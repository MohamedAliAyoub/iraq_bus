<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FleetType extends Model
{
    protected $guarded = ['id'];
    protected $appends = ['image_url' , 'total_seats'];
    protected $casts = [
        'deck_seats' => 'object',
        'facilities' => 'array'
    ];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('assets/images/fleet_type/' . $this->image) : null;
    }
    public function getTotalSeatsAttribute()
    {
        return array_sum($this->deck_seats?? []) ;
    }
    public function vehicles(){
        return $this->hasMany(Vehicle::class);
    }

    public function activeVehicles(){
        return $this->hasMany(Vehicle::class)->where('status', 1);
    }

    //scope active
    public function scopeActive(){
        return $this->where('status', 1);
    }
    
    public function users()
    {
        return $this->hasMany(User::class);
    }


}
