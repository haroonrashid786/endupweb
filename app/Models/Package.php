<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;


    protected $fillable = [
        'weight',
        "width",
        "height",
        "length",
        "image",
        'is_active'
    ];
}
