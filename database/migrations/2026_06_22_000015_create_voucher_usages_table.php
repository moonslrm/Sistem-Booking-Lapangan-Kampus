<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voucher_usages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('voucher_id')->constrained('vouchers')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->decimal('discount_amount', 10, 2);
            $table->timestamp('used_at')->useCurrent();
            $table->timestamps();

            $table->index(['voucher_id', 'user_id']);
            $table->index('booking_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_usages');
    }
};