<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminRole extends Model
{
    use HasFactory;

    public function admin()
    {
        return $this->belongsTo('App\Models\Admin');
    }

    public function roles()
    {
        return $this->belongsTo('App\Models\Role');
    }

    
}
