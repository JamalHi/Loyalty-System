<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'point',
        'discount',
        'accept',
        'user_id',
        'counter',
        ];
    public function user_vouchers(): HasMany
    {
        return $this->hasMany(User_Voucher::class, 'voucher_id');
    }
    public function partner(){
        return $this->belongsTo(User::class , 'user_id');
    }
}
