<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StatusUpdate extends Mailable
{
    use Queueable, SerializesModels;
    protected $message;
    protected $details;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message, $details)
    {
        //
        $this->message = $message;
        $this->details = $details;
    }

    public function build()
    {
        $message = $this->message;
        $order_details = $this->details;

        return $this->view('email.dispatched', compact('order_details', 'message'))->subject('Order Dispatched | '.$order_details->order_number);
    }
}
