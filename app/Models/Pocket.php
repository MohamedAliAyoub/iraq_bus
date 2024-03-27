<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pocket  extends Model
{
    use HasFactory;
    protected $table = "pocket";
    protected $guarded = ['created_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
