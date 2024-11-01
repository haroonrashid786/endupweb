<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'mobile',
        'address',
        'phone',
        'signup_fb',
        'signup_google',
        'profile_picture',
        'last_login',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        //        'password',
        'remember_token',
        //        'two_factor_recovery_codes',
//        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */

    public function password()
    {
        return $this->hasOne(Password::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Roles::class, 'user_roles', 'user_id', 'role_id');
    }

    public function rider()
    {
        return $this->hasOne(Rider::class);
    }

    public function retailer()
    {
        return $this->hasOne(Retailer::class);
    }

    public function assignedOrders()
    {
        return $this->hasMany(OrderAssignment::class, 'user_id');
    }

    public function assignedBlocks()
    {
        return $this->hasMany(Block::class, 'user_id');
    }

    public function assignedCollections()
    {
        return $this->hasMany(Collection::class, 'user_id');
    }
    public function assignedCollectionsReturn()
    {
        return $this->hasMany(Collection::class, 'user_id')->where('return', 1);
    }
    public function assignedReturns()
    {
        return $this->hasMany(ReturnBlock::class, 'user_id');
    }

    public function isAdmin()
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->name == 'Admin') {
                return true;
            }
        }

        return false;
    }

    public function isRetailer()
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->name == 'Retailer') {
                return true;
            }
        }

        return false;
    }

    public function retailer_tickets()
    {
        return $this->hasMany(RetailerTicket::class, 'user_id');
    }

    // For Messages

    public function sentMessages()
    {
        return $this->hasMany(Messages::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Messages::class, 'recipient_id');
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversations::class, 'conversations_user')->withTimestamps();
    }

    // For Messages

    public function auth_logs(){
        return $this->hasMany(AuthLog::class, 'user_id')->latest();
    }

}
