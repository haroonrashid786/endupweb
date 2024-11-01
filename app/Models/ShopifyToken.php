<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopifyToken extends Model
{
    use HasFactory;

    protected $fillable=[
            'user_id',
            'access_token',
            'fulfillment_service_id',
    ];
}
