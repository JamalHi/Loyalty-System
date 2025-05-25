<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission_Role extends Model
{
    use HasFactory;
    protected $table = "permissions_roles";
    protected $fillable = [
        'role_id',
        'permission_id'
        ];
    public function permission(){
        return $this->belongsTo(Permission::class,'permission_id');
    }
}
