<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopifyPackage extends Model
{
    use HasFactory;

    protected $fillable=[

       'retailer_id',
       'name',
       'price',
       'status'
    ];

    public function businessHours()
    {
        return $this->hasMany(ShopifyBusinessHour::class);
    }
}
