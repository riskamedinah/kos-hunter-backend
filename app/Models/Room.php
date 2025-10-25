<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'kos_id',
        'name',
        'status',
    ];

    public function kos()
    {
        return $this->belongsTo(Kos::class);
    }
}
