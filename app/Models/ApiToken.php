<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'token',
        'name',
        'last_used_at',
        'expires_at'
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime'
    ];

    public function admin(){
        return $this->belongsTo(Admin::class);
    }

    public function scopeValid($query){
        return $query->whereNll('expires_at')->orWhere('expires_at', '>', now());
    
    }
}
