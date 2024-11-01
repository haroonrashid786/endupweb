<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
  use HasFactory;

  protected $fillable = [
    'code',
    'value',
    'date_start_expiry',
    'date_end_expiry',
    'single_time',
    'for_express',
    'for_domestic',
    'for_international',
    'status',
  ];
}
