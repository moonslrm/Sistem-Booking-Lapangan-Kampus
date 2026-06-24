<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendReminderNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private int $bookingId, private string $reminderType)
    {
        $this->onQueue('default');
    }

    public function handle(NotificationService $notificationService): void
    {
        $booking = Booking::query()->find($this->bookingId);

        if (! $booking || $booking->status === 'cancelled') {
            return;
        }

        $notificationService->notifyBookingReminder($booking, $this->reminderType);
    }

    public function failed(\Throwable $exception): void
    {
        \Illuminate\Support\Facades\Log::channel('notification')->error(
            'SendReminderNotificationJob failed',
            ['booking_id' => $this->bookingId, 'reminder_type' => $this->reminderType, 'error' => $exception->getMessage()]
        );
    }
}
