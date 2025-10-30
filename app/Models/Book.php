<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

protected $fillable = [
    'kos_id',
    'user_id',
    'start_date',
    'end_date',
    'status',
];

 public function kos()
{
    return $this->belongsTo(Kos::class, 'kos_id');
}

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
