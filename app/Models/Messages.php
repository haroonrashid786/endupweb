<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Messages extends Model
{
    use HasFactory;
        
        protected $fillable=[

        'conversations_id',
        'recipient_id',
        'sender_id',
        'message',
        'read_at'

        
        ];

        public function markAsRead()
    {
        if (Auth::id() == $this->recipient_id && !$this->read_at) {
            $this->read_at = now();
            $this->save();
        }

        return $this;
    }

        public function conversation()
        {
            return $this->belongsTo(Conversations::class);
        }
        
        public function sender()
        {
            return $this->belongsTo(User::class, 'sender_id');
        }
        
        public function recipient()
        {
            return $this->belongsTo(User::class, 'recipient_id');
        }

        public function media()
        {
        return $this->hasMany(MessageMedia::class,'messages_id');
        }

        
}
