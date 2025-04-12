<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RetailOrder extends Model
{
    use HasFactory, SoftDeletes;
    protected $softDelete = true;

    public function store()
    {
        return $this->belongsTo("App\Models\RetailStore");
    }

    public function branch()
    {
        return $this->belongsTo("App\Models\RetailBranch");
    }

    public function retail_order_products()
    {
        return $this->belongsToMany("App\Models\Product", 'retail_order_products', 'order_id', 'product_id')->withPivot('price', 'quantity', 'comms', 'cogs');
    }

    public function salesAdminName()
    {
        if ($this->sales_admin == 1) {
            return "Kettony Tan";
        } else if ($this->sales_admin == 2) {
            return "Edelyn Aro";
        } else if ($this->sales_admin == 3) {
            return "Rhox & Ket";
        } else if ($this->sales_admin == 4) {
            return "Roxelle";
        } else if ($this->sales_admin == 5) {
            return "Sarah";
        } else if ($this->sales_admin == 6) {
            return "Carl John";
        } else if ($this->sales_admin == 7) {
            return "Blesidy";
        } else if ($this->sales_admin == 8) {
            return "Ronnel";
        } else if ($this->sales_admin == 9) {
            return "Rosemarie";
        } else {
            return "";
        }
    }
}