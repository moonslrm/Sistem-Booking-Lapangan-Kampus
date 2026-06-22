<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('venue_id')->constrained('venues')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->unique('booking_id');
            $table->index(['venue_id', 'is_visible']);
            $table->index(['user_id', 'venue_id']);
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('alter table reviews add constraint reviews_rating_check check (rating between 1 and 5)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};