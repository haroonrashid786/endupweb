<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnBlockOrderDistance extends Model
{
    use HasFactory;

    protected $fillable = ['return_block_id', 'order_id', 'distance'];
}
