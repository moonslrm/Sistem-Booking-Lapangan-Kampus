<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table): void {
            $table->id();
            $table->string('booking_code')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('venue_id')->constrained('venues')->cascadeOnDelete();
            $table->foreignId('slot_id')->constrained('venue_slots')->cascadeOnDelete();
            $table->date('booking_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('duration_hours', 4, 2);
            $table->decimal('price_per_hour', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('final_price', 10, 2);
            $table->string('voucher_code')->nullable();
            $table->boolean('is_campus_price')->default(false);
            $table->enum('status', ['pending_payment', 'confirmed', 'checked_in', 'completed', 'cancelled', 'expired', 'failed'])->default('pending_payment');
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['venue_id', 'booking_date', 'status']);
            $table->index(['slot_id', 'booking_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};