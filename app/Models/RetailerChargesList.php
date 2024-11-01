<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetailerChargesList extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'active',
    ];
    public function listItems(){
        return $this->hasMany(RetailerChargesListItem::class, 'retailer_charges_list_id');
    }

    public function retailers()
    {
        return $this->belongsToMany(Retailer::class, 'retailer_charges', 'retailer_charges_list_id', 'retailer_id');
    }
}
