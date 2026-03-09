<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Courier extends Model
{
    use HasFactory, SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'status',
        'is_active'
    ];

    public function getActiveName() {
        if ($this->is_active == 1) {
            return "Yes";
        } else {
            return "No";
        }
    }
}
