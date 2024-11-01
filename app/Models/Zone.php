<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'active'];

    public function riders()
    {
        return $this->belongsToMany(Rider::class, 'rider_zones', 'zone_id', 'rider_id');
    }

    public function postalcodes(){
        return $this->hasMany(PostalCode::class, 'zone_id');
    }

    public function scopeActive($query){
        return $query->where('active',1);
    }

    public function orders(){
        return $this->hasMany(Orders::class, 'zone_id');
    }
}
