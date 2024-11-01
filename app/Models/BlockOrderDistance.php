<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockOrderDistance extends Model
{
    use HasFactory;

    protected $fillable = ['block_id', 'order_id', 'distance'];
}
