<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    public function purchaseOrder()
    {
        return $this->belongsTo('App\Models\PurchaseOrder', 'purchase_order_id');
    }

    public function purchase_orders()
    {
        return $this->belongsToMany("App\Models\Product", 'purchase_orders', 'purchase_id', 'product_id')->withPivot('quantity', 'distributor_price', 'stocks_left');
    }

}
