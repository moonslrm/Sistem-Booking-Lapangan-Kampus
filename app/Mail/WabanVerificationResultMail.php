<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WabanVerificationResultMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public bool $approved, public ?string $reason = null)
    {
    }

    public function build(): self
    {
        $subject = $this->approved ? 'Verifikasi Waban Disetujui' : 'Verifikasi Waban Ditolak';

        return $this->subject($subject)
            ->markdown('emails.waban-verification-result')
            ->with([
                'approved' => $this->approved,
                'reason' => $this->reason,
            ]);
    }
}
