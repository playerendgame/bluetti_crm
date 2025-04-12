<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPaymentMethod extends Model
{
    use HasFactory;

    public function mode_of_payment()
    {
        return $this->belongsTo('App\Models\ModeOfPayment', 'payment_method_id');
    }

    public function admin()
    {
        return $this->belongsTo('App\Models\Admin');
    }

    public function order()
    {
        return $this->belongsTo('App\models\Order');
    }
}
