<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'offer_id'
    ];

    public function offers(){
        return $this->belongsTo(Offer::class , 'offer_id');
    }
}
