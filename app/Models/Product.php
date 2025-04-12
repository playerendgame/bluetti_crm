<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $softDelete = true;
    protected $dates = ["deleted_at"];

    public function purchase_order()
    {
        return $this->belongsToMany("App\Models\Product", 'purchase_orders', 'product_id', 'purchase_id')->withPivot('quantity', 'distributor_price');
    }

    //associated OrderProduct Modell to Product Model 
    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class, 'product_id', 'id');
    }

    public function product_category(){
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }
}
