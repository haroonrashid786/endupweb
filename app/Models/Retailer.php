<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retailer extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_type_id',
        'secret_key',
        'public_key',
        'website',
        'support_email',
        'support_mobile',
        'whatsapp',
        'facebook',
        'instagram',
        'logo',
        'licensefile',
        'currency_id',
        'latitude',
        'longitude',
        'address',
    ];

    protected $hidden = [
        'secret_key',
        'public_key',
        'licensefile',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function businessTypes(){
        return $this->hasOne(BusinessType::class, 'business_type_id');
    }

    public function price(){
        return $this->hasOne(RetailerPrice::class, 'retailer_id');
    }

    public function currency(){
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function orders(){
        return $this->hasMany(Orders::class, 'retailer_id');
    }

    public function promotion(){
        return $this->hasOne(RetailerPromotion::class, 'retailer_id');
    }

    public function charges()
    {
        return $this->belongsToMany(RetailerChargesList::class, 'retailer_charges', 'retailer_id', 'retailer_charges_list_id');
    }

    public function activePromotion(){
        return $this->hasOne(RetailerPromotion::class, 'retailer_id')->whereDate('start_date', '<=', date('Y-m-d'))->whereDate('end_date', '>=',date('Y-m-d'));
    }

    public function documents(){
        return $this->hasMany(RetailerDocument::class, 'retailer_id');
    }
}
