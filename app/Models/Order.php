<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;
    protected $softDelete = true;

    //Had to add this columns for the update function
    protected $fillable = [
        'customer_id', 
        'order_number',
        'contact_number',
        'email',
        'region_id',
        'province_id',
        'city_id',
        'address',
       'order_date',
    'attribution_id',
       'delivery_status',
       'target_delivery_date',
       'dispatch_date',
       'date_delivered',
       'returned_date',
       'courier_id',
       'notes',
       'admin_id',
       'mark_as_paid',
       'tracking_number',
       'deleted_at',
       'created_at',
       'updated_at',
       'mode_of_payment_id',
       'order_date',

    ];
    

    public function customers()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    public function admin()
    {
        return $this->belongsTo('App\Models\Admin');
    }

    public function order_products()
    {
        return $this->belongsToMany("App\Models\Product", 'order_products', 'order_id', 'product_id')->withPivot('quantity', 'price', 'discount', 'cogs');
    }

    public function products()
    {
        return $this->belongsToMany("App\Models\Product", 'order_products', 'order_id', 'product_id')
                    ->withPivot('quantity', 'price', 'discount', 'cogs');
    }

    public function order_product_items()
    {
        return $this->hasMany('App\Models\OrderProduct', 'order_id');
    }



    public function referral()
    {
        return $this->belongsTo("App\Models\Referral");
    }

    public function attribution()
    {
        return $this->belongsTo("App\Models\Attribution");
    }

    public function deliveryStatusName()
    {
        if ($this->delivery_status == 0) {
            return "Pending";
        } else if ($this->delivery_status == 1) {
            return "Shipped";
        } else if ($this->delivery_status == 2) {
            return "Delivered";
        } else if ($this->delivery_status == 3) {
            return "RTS";
        } else if ($this->delivery_status == 4) {
            return "Returned";
        } else if ($this->delivery_status == 5) {
            return "Out For Delivery";
        } else if ($this->delivery_status == 6) {
            return "Cancelled";
        }
        
    }

    public function markAsPaid()
    {
        if ($this->mark_as_paid == 1) {
            return "Paid";
        } else if ($this->mark_as_paid == 0) {
            return "Unpaid";
        }
    }

    public function courier()
    {
        return $this->belongsTo('App\Models\Courier');
    }

    public function mode_of_payment()
    {
        return $this->belongsTo('App\Models\ModeOfPayment', 'mode_of_payment_id');
    }

    public function cities(){
        return $this->belongsTo('App\Models\City', 'city_id');
    }

    public function regions(){
        return $this->belongsTo('App\Models\Region', 'region_id');
    }

    public function provinces(){
        return $this->belongsTo('App\Models\Province', 'province_id');
    }
    
    public function order_history(){
        return $this->hasMany('App\Models\OrderHistory', 'order_id');
    }

    // public function paymentMethods()
    // {
    //     return $this->hasMany(OrderPaymentMethod::class);
    // }


}
