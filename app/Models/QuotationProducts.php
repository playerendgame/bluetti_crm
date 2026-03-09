<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationProducts extends Model
{
    use HasFactory;

    protected $table = 'quotation_products';

    protected $fillable = [
        'quotations_id',
        'product_id',
        'price',
        'quantity',
        'discount',
    ];  

    public function quotations(){
        return $this->belongsTo('App\Models\Quotations', 'quotations_id');
    }

    public function products(){
        return $this->belongsTo('App\Models\Product', 'product_id');
    }
}
