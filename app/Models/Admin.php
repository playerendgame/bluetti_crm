<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Authenticatable
{
    use HasFactory, SoftDeletes;
    protected $softDelete = true;
    protected $dates = ["deleted_at"];

    protected $guard = 'admins';
    
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'api_token'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function admin_roles()
    {
        return $this->belongsToMany('App\Models\Role', 'admin_roles', 'admin_id', 'role_id')->withPivot('create', 'read', 'update', 'delete');
    }

    public function apiTokens(){
        return $this->hasMany(ApiToken::class);
    }

    public function fullName()
    {
        return $this->first_name . " " . $this->last_name;
    }

    public function activityLogs(){
        return $this->hasMany(ActivityLogs::class);
    }

    /**
     * Check if the admin has a specific permission.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        foreach ($this->admin_roles as $role) {
            if ($role->permissions->contains('name', $permission)) {
                return true;
            }
        }
        return false;
    }
}