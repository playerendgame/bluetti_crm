<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $fillable = [
        'name',
        'number',
        'email',
    ];


    public function orders()
    {
        return $this->hasMany('App\Models\Order', 'customer_id');
    }

    public function showOrders(){

        return $this->hasMany('App\Models\Order');

    }

    public function isNewCustomer(){
        return $this->orders()->count() <= 1;
    }

    public function quotations(){
        return $this->hasMany('App\Models\Quotations', 'customer_id');
    }

}
