<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'society_id',
        'room_id',
        'check_in_date',
        'check_out_date',
        'total_price',
        'status',
    ];

    public function society()
    {
        return $this->belongsTo(User::class, 'society_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
