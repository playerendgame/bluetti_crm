<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderHistory extends Model
{
    use HasFactory;

    public function orders()
    {
        $this->belongsTo('App\Models\Order');
    }

    public function admin()
    {
        return $this->belongsTo('App\Models\Admin', 'admin_id');
    }
}
