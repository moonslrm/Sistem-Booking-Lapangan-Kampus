<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venues', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('sport_type', ['futsal', 'badminton', 'basket', 'voli', 'tenis']);
            $table->text('description')->nullable();
            $table->text('location')->nullable();
            $table->json('facilities')->nullable();
            $table->foreignId('managed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['sport_type', 'is_active']);
            $table->index('managed_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};