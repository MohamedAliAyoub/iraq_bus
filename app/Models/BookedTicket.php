<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookedTicket extends Model
{
    use HasFactory;
    protected $guarded = ['created_at'];

    protected $casts = [
        'source_destination' => 'array',
        'seats' => 'array',
        'seats_back' => 'array'

    ];

    protected $appends = ['photo'];

    public function getPhotoAttribute(){
    return $this->where('status', 0);
    }

    public function trip(){
        return $this->belongsTo(Trip::class);
    }
    public function pickup(){
        return $this->belongsTo(Counter::class, 'pickup_point');
    }
    public function drop(){
        return $this->belongsTo(Counter::class, 'dropping_point');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function bookedSeats()
    {
        return $this->hasMany(BookedSeat::class);
    }

    public function history()
    {
        return $this->hasMany(History::class);
    }
    //scope
    public function scopePending(){
        return $this->where('status', 2);
    }

    public function scopeBooked(){
         
        $user = auth()->user();
         
        if ($user) {
        return $this->where('status', 1)->where('user_id', $user->id);
        } else {
            return $this->where('status', 1);
        }
    }

    public function scopeRejected(){
        return $this->where('status', 0);
    }
    
        public function scopeCanceled(){
        return $this->where('status', 3);
    }
}
