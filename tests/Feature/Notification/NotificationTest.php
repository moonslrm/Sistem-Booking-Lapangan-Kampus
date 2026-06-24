<?php

namespace Tests\Feature\Notification;

use App\Jobs\SendBookingConfirmationJob;
use App\Mail\BookingConfirmedMail;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Kreait\Firebase\Messaging\Messaging;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_in_app_notification_saved_after_booking_confirmed()
    {
        Queue::fake();
        Mail::fake();

        $messagingMock = \Mockery::mock(Messaging::class);
        $messagingMock->shouldReceive('send')->andReturnTrue();
        $this->app->bind(Messaging::class, fn () => $messagingMock);

        $booking = Booking::factory()->create(['status' => 'pending_payment']);

        $payload = [
            'order_id' => $booking->booking_code,
            'status_code' => '200',
            'gross_amount' => (string) ((int) $booking->final_price),
            'signature_key' => '',
            'transaction_status' => 'settlement',
            'transaction_id' => 'trx-'.Str::random(8),
        ];

        Config::set('midtrans.server_key', 'secret_test_key');
        $payload['signature_key'] = hash('sha512', $payload['order_id'].$payload['status_code'].$payload['gross_amount'].Config::get('midtrans.server_key'));

        app(\App\Services\MidtransService::class)->handleWebhookNotification($payload);

        Queue::assertPushed(SendBookingConfirmationJob::class);

        $job = new SendBookingConfirmationJob($booking->id);
        $job->handle(app(\App\Services\NotificationService::class));

        $this->assertDatabaseHas('notifications', [
            'user_id' => $booking->user_id,
            'channel' => 'in_app',
            'type' => 'booking_confirmed',
            'status' => 'sent',
        ]);

        Mail::assertSent(BookingConfirmedMail::class);
    }

    public function test_mark_as_read_updates_notification_status_and_read_at()
    {
        $user = User::factory()->create();
        $user->assignRole('umum');

        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => 'test',
            'title' => 'Test Notifikasi',
            'body' => 'Isi notifikasi',
            'data' => [],
            'channel' => 'in_app',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/v1/notifications/'.$notification->id.'/read');

        $response->assertStatus(200);
        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'status' => 'read',
        ]);
    }

    public function test_user_can_only_see_their_own_notifications()
    {
        $user = User::factory()->create();
        $user->assignRole('umum');

        $otherUser = User::factory()->create();
        $otherUser->assignRole('umum');

        Notification::create([
            'user_id' => $user->id,
            'type' => 'test',
            'title' => 'Test',
            'body' => 'User notification',
            'data' => [],
            'channel' => 'in_app',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        Notification::create([
            'user_id' => $otherUser->id,
            'type' => 'test',
            'title' => 'Other',
            'body' => 'Other notification',
            'data' => [],
            'channel' => 'in_app',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/notifications');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.title', 'Test');
    }
}
