<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->string('payment_method')->nullable();
            $table->string('payment_gateway')->default('midtrans');
            $table->string('gateway_order_id')->nullable()->unique();
            $table->string('gateway_transaction_id')->nullable();
            $table->string('snap_token')->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'success', 'failed', 'expired', 'refunded'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->json('gateway_response')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_payments');
    }
};