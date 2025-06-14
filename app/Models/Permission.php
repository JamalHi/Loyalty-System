<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Permission extends Model
{
    use HasFactory;
    protected $fillable = [
        'name'
        ];
    public function permissionRoles(): HasMany
    {
        return $this->hasMany(Permission_Role::class, 'permission_id');
    }
}
