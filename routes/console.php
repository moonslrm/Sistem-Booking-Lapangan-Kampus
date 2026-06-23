<?php

use App\Services\BookingService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('bookings:expire-stale', function () {
    $count = app(BookingService::class)->expireStaleBookings();
    $this->info(sprintf('Expired %d stale bookings.', $count));
})->purpose('Expire stale pending bookings after payment timeout');

Artisan::command('bookings:complete-ready', function () {
    $count = app(BookingService::class)->completeReadyBookings();
    $this->info(sprintf('Marked %d bookings as completed.', $count));
})->purpose('Complete bookings whose scheduled slot time has passed');
