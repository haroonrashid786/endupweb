<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderType extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'active', 'image'];


    public function orders()
    {
        return $this->hasMany(orders::class, 'order_type_id');
    }
}
