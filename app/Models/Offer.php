<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'points',
        'start_time',
        'end_time',
        'user_id'
        ];
    public function user(){
        return $this->belongsTo(User::class , 'user_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class, 'offer_id');
    }
}
