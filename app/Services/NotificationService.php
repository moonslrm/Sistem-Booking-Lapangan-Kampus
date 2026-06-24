<?php

namespace App\Services;

use App\Mail\BookingCancelledMail;
use App\Mail\BookingConfirmedMail;
use App\Mail\BookingReminderMail;
use App\Mail\PaymentFailedMail;
use App\Mail\WabanVerificationResultMail;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserFcmToken;
use Carbon\Carbon;
use Illuminate\Mail\Mailable;
use App\Jobs\SendReminderNotificationJob;
use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Mail;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Messaging;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class NotificationService
{
    public function __construct(private ?Messaging $messaging = null)
    {
    }

    public function sendPush(User $user, string $title, string $body, array $data = []): void
    {
        if (! env('NOTIFICATIONS_PUSH_ENABLED', true)) {
            return;
        }

        try {
            if (! $this->messaging) {
                throw new \Exception('Firebase Messaging service not available');
            }

            $tokens = $user->fcmTokens()->where('is_active', true)->get();
            $status = 'sent';
            $sentAt = now();

            foreach ($tokens as $token) {
                try {
                    $message = CloudMessage::withTarget('token', $token->token)
                        ->withNotification(FirebaseNotification::create($title, $body))
                        ->withData(array_map('strval', $data));

                    $this->messaging->send($message);
                    $token->update(['last_used_at' => now()]);
                } catch (MessagingException | FirebaseException $exception) {
                    if ($this->isInvalidFcmToken($exception->getMessage())) {
                        $token->update(['is_active' => false]);
                    }

                    $status = 'failed';
                }
            }

            $this->createNotificationRecord(
                $user,
                'push',
                'push_notification',
                $title,
                $body,
                $data,
                $status,
                $sentAt,
            );
        } catch (\Throwable $exception) {
            \Illuminate\Support\Facades\Log::channel('notification')->error(
                'Push notification failed',
                [
                    'user_id' => $user->id,
                    'title' => $title,
                    'error' => $exception->getMessage(),
                    'trace' => $exception->getTraceAsString(),
                ]
            );
        }
    }

    public function sendEmail(User $user, Mailable $mailable): void
    {
        Mail::to($user->email)->send($mailable);

        $this->createNotificationRecord(
            $user,
            'email',
            'email_notification',
            'Email notifikasi',
            $this->extractEmailBody($mailable),
            [],
            'sent',
            now(),
        );
    }

    public function createInApp(User $user, string $type, string $title, string $body, array $data = []): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'data' => $data,
            'channel' => 'in_app',
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function notifyBookingConfirmed(Booking $booking): void
    {
        $user = $booking->user;
        $title = 'Booking Dikonfirmasi! 🎉';
        $body = sprintf(
            'Booking Anda untuk %s pada %s %s telah dikonfirmasi. Silakan siapkan diri dan lakukan check-in tepat waktu.',
            $booking->venue->name,
            $booking->booking_date->toDateString(),
            $booking->start_time,
        );
        $data = [
            'booking_id' => $booking->id,
            'booking_code' => $booking->booking_code,
            'type' => 'booking_confirmed',
        ];

        $this->sendPush($user, $title, $body, $data);
        $this->sendEmail($user, new BookingConfirmedMail($booking));
        $this->createInApp($user, 'booking_confirmed', $title, $body, $data);
    }

    public function notifyBookingCancelled(Booking $booking, string $reason): void
    {
        $user = $booking->user;
        $title = 'Booking Dibatalkan';
        $body = sprintf(
            'Booking Anda untuk %s pada %s dibatalkan. Alasan: %s',
            $booking->venue->name,
            $booking->booking_date->toDateString(),
            $reason,
        );
        $data = [
            'booking_id' => $booking->id,
            'booking_code' => $booking->booking_code,
            'reason' => $reason,
            'type' => 'booking_cancelled',
        ];

        $this->sendPush($user, $title, $body, $data);
        $this->sendEmail($user, new BookingCancelledMail($booking, $reason));
        $this->createInApp($user, 'booking_cancelled', $title, $body, $data);
    }

    public function notifyPaymentFailed(Booking $booking): void
    {
        $user = $booking->user;
        $title = 'Pembayaran Gagal';
        $body = sprintf(
            'Pembayaran untuk booking %s di %s gagal. Silakan periksa kembali metode pembayaran Anda.',
            $booking->booking_code,
            $booking->venue->name,
        );
        $data = [
            'booking_id' => $booking->id,
            'booking_code' => $booking->booking_code,
            'type' => 'payment_failed',
        ];

        $this->sendPush($user, $title, $body, $data);
        $this->sendEmail($user, new PaymentFailedMail($booking));
        $this->createInApp($user, 'payment_failed', $title, $body, $data);
    }

    public function notifyVerificationResult(User $user, bool $approved, ?string $reason = null): void
    {
        $title = $approved ? 'Verifikasi Waban Disetujui' : 'Verifikasi Waban Ditolak';
        $body = $approved
            ? 'Status verifikasi kampus Anda telah disetujui. Selamat! Anda dapat melanjutkan proses booking dengan harga khusus kampus.'
            : sprintf('Verifikasi kampus Anda ditolak. Alasan: %s', $reason ?? 'Tidak disebutkan.');
        $data = [
            'approved' => $approved,
            'reason' => $reason,
            'type' => 'verification_result',
        ];

        $this->sendPush($user, $title, $body, $data);
        $this->sendEmail($user, new WabanVerificationResultMail($approved, $reason));
        $this->createInApp($user, 'verification_result', $title, $body, $data);
    }

    public function notifyBookingReminder(Booking $booking, string $reminderType): void
    {
        $user = $booking->user;
        $title = $reminderType === 'h1'
            ? 'Pengingat Booking - 24 Jam Lagi'
            : 'Pengingat Booking - 1 Jam Lagi';

        $body = sprintf(
            'Ingat, booking Anda untuk %s di %s akan dimulai pada %s %s. Harap tiba tepat waktu.',
            $booking->venue->name,
            $booking->venue->name,
            $booking->booking_date->toDateString(),
            $booking->start_time,
        );

        $data = [
            'booking_id' => $booking->id,
            'booking_code' => $booking->booking_code,
            'reminder_type' => $reminderType,
            'type' => 'booking_reminder',
        ];

        $this->sendPush($user, $title, $body, $data);
        $this->sendEmail($user, new BookingReminderMail($booking, $reminderType));
        $this->createInApp($user, 'booking_reminder', $title, $body, $data);
    }

    public function scheduleReminders(Booking $booking): void
    {
        $startAt = Carbon::parse(sprintf('%s %s', $booking->booking_date->toDateString(), $booking->start_time));
        $h1At = $startAt->copy()->subHours(24);
        $h0At = $startAt->copy()->subHour();

        if ($h1At->isFuture()) {
            SendReminderNotificationJob::dispatch($booking->id, 'h1')->delay($h1At);
        }

        if ($h0At->isFuture()) {
            SendReminderNotificationJob::dispatch($booking->id, 'h0')->delay($h0At);
        }
    }

    protected function createNotificationRecord(
        User $user,
        string $channel,
        string $type,
        string $title,
        string $body,
        array $data = [],
        string $status = 'sent',
        ?\DateTimeInterface $sentAt = null,
    ): Notification {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'data' => $data,
            'channel' => $channel,
            'status' => $status,
            'sent_at' => $sentAt ?? now(),
        ]);
    }

    protected function isInvalidFcmToken(string $message): bool
    {
        $invalidClauses = [
            'invalid registration token',
            'registration token is not a valid FCM registration token',
            'registration token is not registered',
            'unregistered',
            'not a valid registration token',
        ];

        $lower = strtolower($message);

        foreach ($invalidClauses as $clause) {
            if (str_contains($lower, $clause)) {
                return true;
            }
        }

        return false;
    }

    protected function extractEmailBody(Mailable $mailable): string
    {
        try {
            return strip_tags($mailable->render());
        } catch (\Throwable) {
            return '';
        }
    }
}
