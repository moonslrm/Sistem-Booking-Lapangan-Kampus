<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking, public string $reminderType)
    {
    }

    public function build(): self
    {
        $subject = $this->reminderType === 'h1' ? 'Pengingat Booking 24 Jam Sebelumnya' : 'Pengingat Booking 1 Jam Sebelumnya';

        return $this->subject($subject)
            ->markdown('emails.booking-reminder')
            ->with([
                'booking' => $this->booking,
                'reminderType' => $this->reminderType,
            ]);
    }
}
