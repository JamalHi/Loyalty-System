<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Charity extends Model
{
    use HasFactory;
    protected $fillable = [
        'location',
        'about',
        'id_image',
        'points',
        'points_exp_date',
        'user_id',
    ];
}
