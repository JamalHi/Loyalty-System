<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'media',
        'view_count',
        'valid',
        'user_id'
    ];
    public function user(){
        return $this->belongsTo(User::class , 'user_id');
    }
}
