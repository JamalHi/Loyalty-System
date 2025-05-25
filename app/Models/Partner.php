<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Partner extends Model
{
    use HasFactory;
    protected $table = 'partners';
    protected $fillable = [
        'first_login',
        'location',
        'points',
        'about',
        'service',
        'id_image',
        'commercial_record',
        'category',
        'user_id'
    ];
    public function cashiers(): HasMany
    {
        return $this->hasMany(Cashier::class, 'partner_id');
    }

    public function User(){
        return $this->belongsTo(User::class , 'user_id');
    }
}
