<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    protected  $endusername,
        $enduseremail,
        $endusernumber,
        $pickuplocation, $dropoffdatetime,
        $pickupdatetime,
        $dropofflocation,
        $shippingnotes,
        $orderweight,
        $shippingcharges,
        $discountprice,
        $totalprice,
        $order_number;
    public function __construct(
        $endusername,
        $enduseremail,
        $endusernumber,
        $pickuplocation,
        $pickupdatetime,
        $dropofflocation,
        $dropoffdatetime,
        $shippingnotes,
        $orderweight,
        $shippingcharges,
        $discountprice,
        $totalprice,
        $order_number
    ) {
        $this->endusername = $endusername;
        $this->enduseremail = $enduseremail;
        $this->endusernumber = $endusernumber;
        $this->pickuplocation = $pickuplocation;
        $this->pickupdatetime = $pickupdatetime;
        $this->dropofflocation = $dropofflocation;
        $this->shippingnotes = $shippingnotes;
        $this->orderweight = $orderweight;
        $this->dropoffdatetime = $dropoffdatetime;
        $this->shippingcharges = $shippingcharges;
        $this->discountprice = $discountprice;
        $this->totalprice = $totalprice;
        $this->order_number = $order_number;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Order Confirmation',
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
            view: 'mail.orderconfirmation',
            with: [
                'endusername'  =>   $this->endusername,
                'enduseremail'  =>   $this->enduseremail,
                'endusernumber' =>    $this->endusernumber,
                'pickuplocation' =>    $this->pickuplocation,
                'pickupdatetime' =>    $this->pickupdatetime,
                'dropofflocation' =>        $this->dropofflocation,
                'dropoffdatetime' => $this->dropoffdatetime,
                'shippingnotes' =>  $this->shippingnotes,
                'orderweight' =>      $this->orderweight,
                'shippingcharges' =>   $this->shippingcharges,
                'discountprice' => $this->discountprice,
                'totalprice' =>    $this->totalprice,
                'order_number' =>  $this->order_number
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
