<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLogs extends Model
{
    use HasFactory;

    protected $fillable = ['admin_id', 'activity', 'source' ,'name'];

    public function admins(){
        return $this->belongsTo('App\Models\Admin', 'admin_id');
    }


}
