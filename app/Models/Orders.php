<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;

    public $appends = ['distance_collector_num', 'retailer_address'];
    protected $fillable = [
        'retailer_id',
        'order_number',
        'payment_type',
        'shipping_notes',
        'enduser_name',
        'enduser_email',
        'enduser_address',
        'enduser_mobile',
        'enduser_ordernotes',
        'order_key',
        'order_type',
        'delivery_status',
        'dropoff_country',
        'dropoff_city',
        'dropoff_postal',
        'is_premium',
        'premium_code',
        'collector_delivery_status',
        'order_type_id',
        'enduser_id',
        'zone_id',
        'pickuptime',
        'dropoff_address',
        'pickupdate',
        'number_of_items',
        'is_accepted',
        'deliverydate',
        'deliverytime',
        'pickup_coordinates',
        'dropoff_coordinates',
        'pickup_postal_code',
        'pickup_city',
        'pickup_country',
        'return_delivery_status',
        'return_to_warehouse',
        'undelivered',
        'assigned_to_rider',
        'unddelivered_comments',
        'house_address',
        'delivery_type',
       'pickup_house_number',
       'pickup_street_address',
       'dropoff_street_address',
       'dropoff_house_number',
       'is_shopify',
       'shopify_package_id',
       'order_total',
       'shipping_charges'
    ];

    public function items()
    {
        return $this->hasMany(Items::class, 'order_id');
    }

    public function retailer()
    {
        return $this->belongsTo(Retailer::class, 'retailer_id');
    }

    public function assignment()
    {
        return $this->hasMany(OrderAssignment::class, 'order_id');
    }

    public function block()
    {
        return $this->belongsToMany(Block::class, 'block_orders', 'order_id', 'block_id');
    }

    public function collection()
    {
        return $this->belongsToMany(Collection::class, 'collect_orders', 'order_id', 'collection_id');
    }

    public function toRetailerCollection()
    {
        return $this->belongsToMany(Collection::class, 'collect_orders', 'order_id', 'collection_id')->where('return', 1);
    }

    public function returnBlock()
    {
        return $this->belongsToMany(ReturnBlock::class, 'return_block_orders', 'order_id', 'return_block_id');
    }

    public function delivery_information()
    {
        return $this->hasOne(OrderDeliveryInformation::class, 'order_id');
    }

    public function collector_delivery_information()
    {
        return $this->hasOne(CollectorOrderDeliveryInformation::class, 'order_id');
    }

    public function return_delivery_information()
    {
        return $this->hasOne(ReturnOrderDeliveryInformation::class, 'order_id');
    }

    public function itemsScanInfo()
    {
        return $this->hasMany(ItemLabel::class, 'order_id');
    }
    public function getDistanceCollectorNumAttribute()
    {
        $explode = explode(' ', $this->collector_distance);
        return (float) $explode[0];
    }

    public function getRetailerAddressAttribute()
    {

        if(!is_null($this->retailer)){
            return $this->retailer->address;
        }
        return null;

    }

    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }
    public function orderType()
    {
        return $this->belongsTo(OrderType::class, 'order_type_id');
    }

    public function returnOrder()
    {
        return $this->hasOne(ReturnOrder::class, 'order_id');
    }

    public function statuses()
    {
        return $this->hasMany(OrderStatus::class, 'order_id');
    }

    public function orderDistance(){
        return $this->hasOne(CollectionOrderDistance::class, 'order_id');
    }

    // public function getExtraDistanceAttribute(){
    //     if(!is_null($this->orderDistance)){
    //         return $this->orderDistance->distance;
    //     }
    //     return null;
    // }

    public function deliveryType()
    {
        return $this->hasOne(ShopifyPackage::class,'id','shopify_package_id');
    }

}
