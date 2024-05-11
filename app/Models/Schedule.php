<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    //scope
    public function scopeActive(){
        return  $this->where('status', 1);
    }
}
