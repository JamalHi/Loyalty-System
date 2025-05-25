<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;
    protected $fillable = [
        'operation',
        'transfer_points',
        'transfer_time',
        'invoice',
        'from_user',
        'to_user'
        ];
    public function userFrom(){
        return $this->belongsTo(User::class , 'from_user');
    }
    public function userTo(){
        return $this->belongsTo(User::class , 'to_user');
    }
}
