<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverCarImage extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['image_url'];

    public function user(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class);
    }

    public function driverDetails(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DriverDetails::class, 'driver_details_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('assets/images/driver/' . $this->image) : null;
    }


}
