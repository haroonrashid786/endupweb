<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionOrderDistance extends Model
{
    use HasFactory;
    protected $fillable = ['collection_idp', 'order_id', 'distance'];
}
