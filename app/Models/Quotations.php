<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotations extends Model
{
    use HasFactory;

    protected $table = 'quotations';

    protected $fillable = [
        'customer_id',
        'reference_number'
    ];

    public function customers(){
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    public function quotation_products(){
        return $this->hasMany('App\Models\QuotationProducts', 'quotations_id');
    }
}
