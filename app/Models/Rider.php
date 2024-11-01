<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rider extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'passport',
        'license_number',
        'unique_id',
        'passport_file',
        'license_file',
        'is_collector'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function workingdays()
    {
        return $this->belongsToMany(WorkingDay::class, 'rider_working_days', 'rider_id', 'working_day_id');
    }

    public function zones()
    {
        return $this->belongsToMany(Zone::class, 'rider_zones', 'rider_id', 'zone_id');
    }

    public function location(){
        return $this->hasOne(RidersLocation::class, 'rider_id');
    }
}
