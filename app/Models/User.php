<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public const CLIENT = 1;
    public const AGENT = 2;
    public const DRIVER = 3;
    
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'address' => 'object',
        'ver_code_send_at' => 'datetime'
    ];

    protected $data = [
        'data'=>1
    ];



    protected $appends = ["full_name"];

    public function login_logs()
    {
        return $this->hasMany(UserLogin::class);
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class)->where('status','!=',0);
    }

    public function tickets()
    {
        return $this->hasMany(BookedTicket::class);
    }

    // SCOPES

    public function getFullnameAttribute()
    {
        return $this->firstname . ' ' . $this->lastname;
    }


    public function scopeActive()
    {
        return $this->where('status', 1);
    }

    public function scopeBanned()
    {
        return $this->where('status', 0);
    }

    public function scopeEmailUnverified()
    {
        return $this->where('ev', 0);
    }

    public function scopeSmsUnverified()
    {
        return $this->where('sv', 0);
    }
    public function scopeEmailVerified()
    {
        return $this->where('ev', 1);
    }

    public function scopeSmsVerified()
    {
        return $this->where('sv', 1);
    }
     public function devices()
    {
        return $this->hasMany(Device::class);
    }
 public function pocket()
    {
        return $this->hasOne(Pocket::class);
    }
    public function history()
    {
        return $this->hasMany(History::class);
    }
        public function fleetType(){
        return $this->belongsTo(FleetType::class,'fleet_type_id');
    }

    public function route(){
        return $this->belongsTo(VehicleRoute::class , 'route_id');
    }

    public function driverDetails()
    {
        return $this->hasOne(DriverDetails::class , 'user_id'   );
    }
    public function driverCarImage()
    {
        return $this->hasMany(DriverCarImage::class , 'user_id');
    }

}
