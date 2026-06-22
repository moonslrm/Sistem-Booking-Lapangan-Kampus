<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_qr_codes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->string('token')->unique();
            $table->string('qr_image_path')->nullable();
            $table->boolean('is_used')->default(false);
            $table->timestamp('scanned_at')->nullable();
            $table->foreignId('scanned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique('booking_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_qr_codes');
    }
};