<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthLog extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'ip', 'code', 'is_verified', 'country', 'city'];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
