<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    public function adminRoles(){

        return $this->hasmany(AdminRole::Class);

    }

    public function permissions()
    {
        return $this->belongsToMany(Permissions::class, 'role_permission', 'role_id', 'permission_id');
    }

    public function hasPermission($permissionName)
    {
        return $this->permissions->contains('name', $permissionName);
    }
}
