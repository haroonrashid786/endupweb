<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    use HasFactory;

    protected $fillable=[
        'end_user_id',
        'end_user_id',
        'order_id',
        'order_id',
        'stripe_session_id',
        'order_amount',
    ];
}
