<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Items extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'sku',
        'name',
        'barcode',
        'image',
        'price',
        'quantity',
        'weight',
     
        'length',
        'dimension',
        'height',
        'width',
        'volumetric_weight',
        'verified_by_collector',
        'measuring_unit',
    ];


    public function scan_info()
    {
        return $this->hasOne(ItemLabel::class, 'item_id');
    }

    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }
}
