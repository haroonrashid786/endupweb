<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostalCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'zone_id',
        'postal',
    ];

    public function zone(){
        return $this->belongsTo(Zone::class, 'zone_id');
    }
}
