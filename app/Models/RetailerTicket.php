<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetailerTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'ticket_id', 'subject', 'message'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
