<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leads extends Model
{
    use HasFactory;

    protected $table = 'leads';

    protected $fillable = [
        'customer_id',
        'email',
        'number',
    ];

    public function customers(){
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

}
