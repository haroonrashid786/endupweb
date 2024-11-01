<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversationsUser extends Model
{
    use HasFactory;

    protected $table = 'conversations_user';

    protected $fillable = [
        'conversations_id',
        'user_id',
    ];

    public function conversation()
{
    return $this->belongsTo(Conversations::class, 'conversations_id');
}
    
}
