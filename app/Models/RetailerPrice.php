<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetailerPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'retailer_id',
        'extra_discount_percentage',
        'extra_surcharge',
    ];

    public function retailer(){
        return $this->belongsTo(Retailer::class, 'retailer_id');
    }
}
