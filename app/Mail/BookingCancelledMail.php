<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking, public string $reason)
    {
    }

    public function build(): self
    {
        return $this->subject('Booking Dibatalkan: '.$this->booking->booking_code)
            ->markdown('emails.booking-cancelled')
            ->with([
                'booking' => $this->booking,
                'reason' => $this->reason,
            ]);
    }
}
