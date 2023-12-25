<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        "user_id",
        "date",
        "in_time",
        "out_time",
        "notes",
        "lat",
        "lng",
        "out_lng",
        "out_lat",
        "out_notes",

    ];

    use HasFactory;


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
