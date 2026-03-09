<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attribution extends Model
{
    use HasFactory, SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'category',
        'campaign_name',
        'distribution_channel_id',
        'is_active'
    ];  

    public function getCategoryName()
    {
        if ($this->category == 1) {
            return "Facebook";
        } else if ($this->category == 2) {
            return "Website";
        } else if ($this->category == 3) {
            return "Lazada";
        } else if ($this->category == 4) {
            return "Shopee";
        } else if ($this->category == 5) {
            return "Organic";
        } else if ($this->category == 6) {
            return "Referral";
        } else {
            "";
        }
    }

    public function getIsActiveName()
    {
        if ($this->is_active == 1) {
            return "Yes";
        } else {
            return "No";
        }
    }

    public function campaign_spent()
    {
        return $this->hasOne('App\Models\CampaignSpent', 'attribution_id');
    }

    public function distribution_channel(){
        return $this->belongsTo(DistributionChannel::class, 'distribution_channel_id');
    }

}
