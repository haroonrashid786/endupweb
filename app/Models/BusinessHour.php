<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessHour extends Model
{
    use HasFactory;

    protected $fillable =[
        'day',
        'time_zone',
        'open_time',
        'close_time',
        'break_time_start',
        'break_time_end',
       ];
}
