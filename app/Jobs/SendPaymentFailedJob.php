<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPaymentFailedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private int $bookingId)
    {
        $this->onQueue('default');
    }

    public function handle(NotificationService $notificationService): void
    {
        $booking = Booking::query()->find($this->bookingId);

        if (! $booking) {
            return;
        }

        $notificationService->notifyPaymentFailed($booking);
    }

    public function failed(\Throwable $exception): void
    {
        \Illuminate\Support\Facades\Log::channel('notification')->error(
            'SendPaymentFailedJob failed',
            ['booking_id' => $this->bookingId, 'error' => $exception->getMessage()]
        );
    }
}
