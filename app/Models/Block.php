<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory;


    public function orders()
    {
        return $this->belongsToMany(Orders::class, 'block_orders', 'block_id', 'order_id')->orderBy('distance');
    }

    public function pendingOrders(){
        return $this->belongsToMany(Orders::class, 'block_orders', 'block_id', 'order_id')->where('orders.delivery_status', '!=', 'Delivered')->orderBy('distance');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function ordersDistance(){
        return $this->hasMany(BlockOrderDistance::class, 'block_id');
    }
}
