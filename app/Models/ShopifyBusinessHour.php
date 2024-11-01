<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopifyBusinessHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'shopify_package_id',
        'day',
        'time_zone',
        'open_time',
        'close_time',
        'break_time_start',
        'break_time_end',
    ];
}
