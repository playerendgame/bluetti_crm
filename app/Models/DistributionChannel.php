<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributionChannel extends Model
{
    use HasFactory;

    protected $table = 'distribution_channels';

    public function attribution(){

        return $this->hasOne(Attribution::class, 'distribution_channel_id');

    }
}
