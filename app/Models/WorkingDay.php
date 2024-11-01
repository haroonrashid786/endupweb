<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingDay extends Model
{
    use HasFactory;

    public function riders()
    {
        return $this->belongsToMany(Rider::class, 'rider_working_days', 'working_day_id', 'rider_id');
    }
}
