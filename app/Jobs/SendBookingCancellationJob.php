<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBookingCancellationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private int $bookingId, private string $reason)
    {
        $this->onQueue('default');
    }

    public function handle(NotificationService $notificationService): void
    {
        $booking = Booking::query()->find($this->bookingId);

        if (! $booking) {
            return;
        }

        $notificationService->notifyBookingCancelled($booking, $this->reason);
    }

    public function failed(\Throwable $exception): void
    {
        \Illuminate\Support\Facades\Log::channel('notification')->error(
            'SendBookingCancellationJob failed',
            ['booking_id' => $this->bookingId, 'error' => $exception->getMessage()]
        );
    }
}
