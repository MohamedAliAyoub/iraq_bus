<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookedSeat  extends Model
{
    use HasFactory;
    protected $guarded = ['created_at'];

    public function bookedTicket()
    {
        return $this->belongsTo(BookedTicket::class);
    }
}
