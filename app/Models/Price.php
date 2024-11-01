<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    protected $fillable = [
      'city_from',
      'country_from',
      'postal_code_from',
      'city_to',
      'country_to',
      'postal_code_to',
      'volumetric_weight',
      'length',
      'height',
      'width',
      'quantity_box',
      'price',
      'currency_id',
    ];

    public function scopeheight($val){
        return $this->where('height', '<=',$val);
    }

    public function scopewidth($val){
        return $this->where('width', '<=',$val);
    }

    public function scopelength($val){
        return $this->where('length', '<=',$val);
    }


    public function cityFrom($val){
        return $this->where('city_from', $val);
    }


    public function cityTo($val){
        return $this->where('city_to', $val);
    }

    public function countryFrom($val){
        return $this->where('country_from', $val);
    }

    public function countryTo($val){
        return $this->where('country_to', $val);
    }


    public function postalTo($val){
        return $this->where('postal_code_to', $val);
    }

    public function postalFrom($val){
        return $this->where('postal_code_from', $val);
    }

    public function scopevolumeWeight($val){
        return $this->where('volumetric_weight', '<=',$val);
    }

    public function currency(){
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function shippingTerms(){
        return $this->hasMany(ShippingTerm::class, 'shipping_terms_id');
    }
}
