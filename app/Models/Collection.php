<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasFactory;

    public function orders()
    {
        return $this->belongsToMany(Orders::class, 'collect_orders', 'collection_id', 'order_id')->orderBy('collector_distance');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function ordersDistance(){
        return $this->hasMany(CollectionOrderDistance::class, 'collection_id');
    }
}
