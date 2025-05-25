<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $fillable = [
        'OTP',
        'OTP_exp_date',
        'ad_view_counter',
        'points' ,
        'special_points',
        'points_exp_date',
        'special_exp_date',
        'user_id',
    ];

    public function User(){
        return $this->belongsTo(User::class , 'user_id');
    }
}
