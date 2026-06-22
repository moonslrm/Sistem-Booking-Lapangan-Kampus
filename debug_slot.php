<?php

putenv('APP_ENV=testing');
putenv('CACHE_STORE=array');
putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE='.__DIR__.'/database/debug.sqlite');

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

if (! file_exists(__DIR__.'/database/debug.sqlite')) {
    touch(__DIR__.'/database/debug.sqlite');
}

// Run migrations on sqlite file
$app->make(Illuminate\Contracts\Console\Kernel::class)->call('migrate', ['--force' => true]);

use App\Models\Venue;
use App\Models\VenueSlot;
use App\Models\Booking;
use App\Services\SlotAvailabilityService;

$venue = Venue::factory()->create();
$slot = VenueSlot::factory()->create([
    'venue_id' => $venue->id,
    'day_of_week' => date('w'),
    'start_time' => '10:00:00',
    'end_time' => '12:00:00',
    'is_active' => true,
]);
Booking::factory()->create([
    'venue_id' => $venue->id,
    'slot_id' => $slot->id,
    'booking_date' => date('Y-m-d'),
    'start_time' => '11:00:00',
    'end_time' => '13:00:00',
    'status' => 'confirmed',
]);

$slots = app(SlotAvailabilityService::class)->getAvailableSlots($venue, date('Y-m-d'));
echo json_encode($slots, JSON_PRETTY_PRINT);
