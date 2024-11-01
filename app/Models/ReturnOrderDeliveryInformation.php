<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnOrderDeliveryInformation extends Model
{
    use HasFactory;

    public function order(){
        return $this->belongsTo(Orders::class, 'order_id');
    }
}