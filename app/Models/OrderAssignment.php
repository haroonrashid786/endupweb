<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
      'order_id',
      'user_id',
      'assigning_time',
      'store_pickup_scan_time',
      'depot_pickup_scan_time',
      'delivery_type',
      'sms_notify',
      'email_notify',
      'track_number',
      'expected_time',
      'rider_notes',
      'tracking_id',
      'code_delivery_zone',
      'delivery_at',
      'notes',
      'pickup_location',
      'dropoff_location',
      'pickup_date_time',
      'dropoff_date_time',
      'endup_notes',
    ];

    public function order(){
        return $this->belongsTo(Orders::class, 'order_id')->with('retailer');
    }
}
