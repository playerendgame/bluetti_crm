<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = ['stocks_left', 'product_id', 'distributor_price'];

    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    public function purchases()
    {
        return $this->hasMany('App\Models\Purchase', 'purchase_order_id');
    }

    public function orderProducts()
    {
        return $this->hasMany('App\Models\OrderProduct');
    }

}
