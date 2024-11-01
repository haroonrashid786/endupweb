<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnBlock extends Model
{
    use HasFactory;

    public function orders()
    {
        return $this->belongsToMany(Orders::class, 'return_block_orders', 'return_block_id', 'order_id')->orderBy('return_distance');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function ordersDistance(){
        return $this->hasMany(ReturnBlockOrderDistance::class, 'return_block_id');
    }
}
