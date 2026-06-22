<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venue_photos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('venue_id')->constrained('venues')->cascadeOnDelete();
            $table->string('photo_path');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['venue_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venue_photos');
    }
};