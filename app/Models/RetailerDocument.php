<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetailerDocument extends Model
{
    use HasFactory;

    public function retailer(){
        return $this->belongsTo(Retailer::class, 'retailer_id');
    }
}
