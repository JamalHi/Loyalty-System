<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_Voucher extends Model
{
    use HasFactory;
    protected $table = 'user_vouchers';
    protected $fillable = [
        'exp_date',
        'valid',
        'OTP',
        'OTP_exp_date',
        'user_id',
        'voucher_id'
        ];
    public function user(){
        return $this->belongsTo(User::class , 'user_id');
    }

    public function voucher(){
        return $this->belongsTo(Voucher::class , 'voucher_id');
    }
}
