<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venue_slots', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('venue_id')->constrained('venues')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('price_normal', 10, 2);
            $table->decimal('price_campus', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['venue_id', 'day_of_week', 'start_time', 'end_time'], 'venue_slots_unique_schedule');
            $table->index(['venue_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venue_slots');
    }
};