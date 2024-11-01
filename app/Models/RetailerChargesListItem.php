<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetailerChargesListItem extends Model
{
    use HasFactory;

    public function shopifyPackage(){
        return $this->hasOne(ShopifyPackage::class,'id','shopify_package_id');
    }

    public function retailerCharges(){
        return $this->hasOne(RetailerChargesList::class,'id','retailer_charges_list_id');
    }
}
