<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History  extends Model
{
    use HasFactory;
    protected $table = "history";
    protected $guarded = ['created_at'];

    const BOOK_TICKET = 1;
    const CANCEL_TICKET = 2;


    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function bookedTicket()
    {
        return $this->belongsTo(BookedTicket::class);
    }
}
