<?php

namespace App\Http\Controllers;

use App\Helpers\OptimizeTrait;
use App\Http\Traits\FileUploadTrait;
use App\Mail\EndConversation;
use App\Mail\NewMessageRecieved;
use App\Models\Conversations;
use App\Models\MessageMedia;
use App\Models\Messages;
use App\Models\Retailer;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class MessageController extends Controller
{
    // use FileUploadTrait;


    public function newMessage()
    {

        $stores = User::whereHas('roles', function ($q) {
            $q->where('name', 'Admin');
        })->get();
        return view('messages.new', compact('stores'));

    }

    public function create(User $recipient)
    {
        $conversation = $this->getOrCreateConversation($recipient);

        return redirect()->route('messages.show', $conversation);
    }


    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $conversations = Conversations::with(['messages', 'initiator', 'recipient'])->latest('updated_at')->get();
        } else {
            $conversations = Auth::user()->conversations()->with(['messages', 'initiator', 'recipient'])->latest('updated_at')->get();
        }
        // dd($conversations);
        return view('messages.index', compact('conversations'));
    }

    public function show(Conversations $conversation)
    {
        $conversation->load('messages.sender', 'messages.recipient');
        $messages = $conversation->messages()->orderBy('created_at', 'asc')->get();
        return view('messages.show', compact('conversation', 'messages'));
    }

    public function sendMessage(Request $request, User $recipient)
    {
        // Get the authenticated user
        $sender = auth()->user();
        $participants = [$sender->id, intval($request->recipient_id)];
        // Check if the conversation already exists or create a new one
        // $conversation = Conversations::findOrCreateByParticipants([$sender->id, intval($request->recipient_id)]);

        // sort($participants);

        // $conversation = new Conversations([
        //     'participant_ids' => $participants,
        // ]);
        // $conversation->save();

        $conversation = Conversations::find($request->conversation_id);

        // Create a new message
        $message = new Messages([
            'sender_id' => $sender->id,
            'recipient_id' => intval($request->recipient_id),
            'message' => $request->input('message'),
        ]);

        // Save the message to the conversation
        $conversation->messages()->save($message);


        // $product->images()->insert($images_arr);
        if ($request->hasFile('attachment')) {
            $images = OptimizeTrait::uploadMultipleFiles($request->attachment);
            $attachment_arr = [];
            if (count($images) > 0) {
                foreach ($images as $key => $i) {
                    $attachment_arr[$key]['messages_id'] = $message->id;
                    $attachment_arr[$key]['path'] = $i;
                    $attachment_arr[$key]['type'] = 'media';
                    $attachment_arr[$key]['created_at'] = now();
                    $attachment_arr[$key]['updated_at'] = now();
                }
            }
            $message->media()->insert($attachment_arr);

        }
        // Optionally, handle any attachments to the message

        // Update the conversation's last message ID
        $conversation->update(['last_message_id' => $message->id]);

        $reciever = User::find($request->recipient_id);
        if (!empty($reciever)) {
            $subject = $conversation->subject;
            $text = $request->message;
            // Mail::to($reciever->email)->send(new NewMessageRecieved($subject, $text));
        }
        // Redirect to the conversation's page
        return redirect()->route('messages.show', $conversation)->with('success', 'Message Sent');
    }



    protected function getOrCreateConversation(User $recipient)
    {
        $conversation = Conversations::whereIn('id', function ($query) use ($recipient) {
            $query->select('conversation_id')
                ->from('conversation_user')
                ->whereIn('user_id', [auth()->id(), $recipient->id])
                ->groupBy('conversation_id')
                ->havingRaw('COUNT(DISTINCT user_id) = 2');
        })
            ->first();

        if (!$conversation) {
            $conversation = Conversations::create();
            $conversation->users()->sync([$recipient->id, auth()->id()]);
        }

        return $conversation;
    }


    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'message' => 'required',
        ]);

        // $user = User::find(Auth::user()->id);
        // $open_conversations = $user->conversations()->where('recipient_id', $request->recipient_id)->count();
        // if ($open_conversations > 0) {
        //     return redirect()->back()->with('error', 'You cannot create a new conversation while you have open conversations.');
        // }

        $recipient = User::findOrFail($request->recipient_id);

        // Check if a conversation already exists between the two users
        // $conversation = Conversations::where(function ($query) use ($recipient) {
        //     $query->where('initiator_id', auth()->user()->id)
        //         ->where('recipient_id', $recipient->id);
        //     ;
        // })->orWhere(function ($query) use ($recipient) {
        //     $query->where('initiator_id', $recipient->id)
        //         ->where('recipient_id', auth()->user()->id);
        //     ;
        // })->first();
        // if (!$conversation) {
        // Create a new conversation
        $ticket_no = OptimizeTrait::generateUniqStr();
        $conversation = Conversations::create([
            'initiator_id' => auth()->user()->id,
            'recipient_id' => $recipient->id,
            'subject' => $request->subject,
            'ticket_no' => $ticket_no,
        ]);
        $conversation->users()->attach([$conversation->initiator_id, $conversation->recipient_id]);
        // }

        // Create a new message
        $message = Messages::create([
            'conversations_id' => $conversation->id,
            'sender_id' => auth()->user()->id,
            'recipient_id' => $recipient->id,
            'message' => $request->message,
        ]);

        // Upload any media files and associate them with the message
        if ($request->attachment) {
            $images = OptimizeTrait::uploadMultipleFiles($request->attachment);
            $attachment_arr = [];
            if (count($images) > 0) {
                foreach ($images as $key => $i) {
                    $attachment_arr[$key]['messages_id'] = $message->id;
                    $attachment_arr[$key]['path'] = $i;
                    $attachment_arr[$key]['type'] = 'media';
                    $attachment_arr[$key]['created_at'] = now();
                    $attachment_arr[$key]['updated_at'] = now();
                }
            }
            $message->media()->insert($attachment_arr);

        }

        $reciever = User::find($request->recipient_id);
        if (!empty($reciever)) {
            $subject = $request->subject;
            $text = $request->message;
            Mail::to($reciever->email)->send(new NewMessageRecieved($subject, $text, $ticket_no));
        }

        return redirect()->route('messages.index')->with('success', 'Ticket Generated Successfully');
    }


    public function endConversation(Conversations $conversation)
    {
        $conversation->status = 1;
        $conversation->save();

        $reciever = User::find($conversation->recipient_id);
        $sender = User::find($conversation->initiator_id);
        // Mail::to($reciever->email)
        //     ->cc($sender->email)
        //     ->send(new EndConversation($conversation));
        return redirect()->route('messages.index')->with('success', 'Ticket closed successfully');
    }


}
