<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cashier extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'partner_id'
    ];

    public function partner(){
        return $this->belongsTo(Partner::class , 'partner_id');
    }
    public function User(){
        return $this->belongsTo(User::class , 'user_id');
    }
}
