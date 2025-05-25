<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin_Action extends Model
{
    use HasFactory;
    protected $fillable = [
        'operation',
        'date',
        'name',
        'admin_id'
    ];
    public function user(){
        return $this->belongsTo(User::class , 'admin_id');
    }
}
