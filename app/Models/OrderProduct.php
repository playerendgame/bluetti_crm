<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    //protected $fillable = ['cogs', 'po_id'];

    protected $fillable = ['order_id', 'product_id', 'quantity', 'price', 'discount', 'cogs', 'po_id'];

    use HasFactory;

    public function order()
    {
        return $this->belongsTo('App\Models\Order');  
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    public function purchase_orders()
    {
        return $this->hasMany('App\Models\PurchaseOrder');
    }
}
