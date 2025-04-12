<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    public function region()
    {
        return $this->belongsTo('App\Models\Region');
    }

    public function order(){
        return $this->hasOne('App\Models\Order', 'province_id');
    }
}
