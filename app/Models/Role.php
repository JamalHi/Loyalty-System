<?php

namespace App\Models;

use App\Models\Permission_Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;
    protected $fillable = [
        'role'
        ];
    public function user(): HasMany
    {
        return $this->hasMany(User::class, 'role_id');
    }

    public function permissionRoles(): HasMany
    {
        return $this->hasMany(Permission_Role::class, 'role_id');
    }

    public function check($param){
        $permission = permission::query()->where('name' , '=' , $param)->first();

        return Permission_Role::query()
            ->where('permission_id' , '=' , $permission->id)
            ->where('role_id' , '=' , $this->id)
            ->exists();
    }
}
