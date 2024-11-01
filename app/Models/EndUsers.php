<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class EndUsers extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;
    protected $guard = "end_user";

    protected $fillable = [
        'firstname', 'number', 'password', "lastname", "email", "location_str", "location_cod", "code", "login_with", 'profile_picture','device_token'
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];
    public function tickets(){
        return $this->hasMany(Ticket::class, 'user_id');
    }
}
