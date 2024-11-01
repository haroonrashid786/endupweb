<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversations extends Model
{
    use HasFactory;

  protected $fillable=[
        'initiator_id',
        'recipient_id',
        'subject',
        'status',
        'ticket_no',

  ];


            public function unreadMessagesCount()
            {
                if ($this->messages->isEmpty()) {
                    return 0;
                }

                $unreadCount = 0;

                foreach ($this->messages as $message) {
                    if (!$message->read_at && $message->sender_id != auth()->id()) {
                        $unreadCount++;
                    }
                }

                return $unreadCount;
            }


  public static function findOrCreateByParticipants($participants)
{
    // Sort the array of participant IDs
    sort($participants);

    // Get the existing conversation with the given participants
    $conversation = Conversations::whereIn('initiator_id', $participants)->whereIn('recipient_id',$participants)
    // ->where('status',0)
    ->first();
    // If no conversation exists, create a new one
    if (!$conversation) {
        $conversation = new Conversations([
            'participant_ids' => $participants,
        ]);
        $conversation->save();
    }

    return $conversation;
}


public function messages()
{
    return $this->hasMany(Messages::class);
}

public function initiator()
{
    return $this->belongsTo(User::class, 'initiator_id')->with('retailer:user_id,id,website');
}

public function recipient()
{
    return $this->belongsTo(User::class, 'recipient_id');
}

public function users()
{
    return $this->belongsToMany(User::class);
}

public function lastMessage()
{
    return $this->hasOne(Messages::class)->latest();
}


}
