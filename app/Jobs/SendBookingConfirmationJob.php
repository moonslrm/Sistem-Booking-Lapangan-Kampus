<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBookingConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private int $bookingId)
    {
        $this->onQueue('default');
    }

    public function handle(): void
    {
        Log::channel('booking')->info('SendBookingConfirmationJob executed.', ['booking_id' => $this->bookingId]);
        // Placeholder: actual notification/email logic will be implemented in P12
    }
}
