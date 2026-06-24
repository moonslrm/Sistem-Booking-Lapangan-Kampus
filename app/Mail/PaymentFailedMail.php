<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentFailedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
    }

    public function build(): self
    {
        return $this->subject('Pembayaran Gagal: '.$this->booking->booking_code)
            ->markdown('emails.payment-failed')
            ->with(['booking' => $this->booking]);
    }
}
