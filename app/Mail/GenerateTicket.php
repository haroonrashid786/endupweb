<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GenerateTicket extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $msg, $mail_subject, $user_name, $user_phone_number;
    public function __construct($msg, $mail_subject,  $user_name, $user_phone_number)
    {
        $this->msg = $msg;
        $this->user_name = $user_name;
        $this->user_phone_number = $user_phone_number;
        $this->mail_subject = $mail_subject;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: "$this->mail_subject",
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'mail.tickets',
            with: [
                'msg' => $this->msg,
                'user_name' => $this->user_name,
                'phone_number' => $this->user_phone_number,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
