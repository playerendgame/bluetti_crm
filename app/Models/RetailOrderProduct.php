<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetailOrderProduct extends Model
{
    protected $fillable = ['order_id', 'product_id', 'quantity', 'price', 'comms', 'cogs', 'po_id'];

    use HasFactory;

    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    public function retail_order()
    {
        return $this->belongsTo('App\Models\RetailOrder');  
    }
}
