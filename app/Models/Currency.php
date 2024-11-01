<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
      'code',
      'status',
    ];

    public function scopeActive($query){
        return $query->where('status',1);
    }
}
