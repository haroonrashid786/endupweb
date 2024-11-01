<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Password extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'password',
    ];

    protected $hidden = [
        'password'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
