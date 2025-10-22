<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kos extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'address',
        'price_per_month',
        'gender'
    ];

    public function facilities()
    {
        return $this->hasMany(KosFacility::class);
    }

    public function images()
    {
        return $this->hasMany(KosImage::class);
    }
}
