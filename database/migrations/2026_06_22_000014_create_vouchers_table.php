<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed']);
            $table->decimal('discount_value', 10, 2);
            $table->decimal('min_booking_amount', 10, 2)->default(0);
            $table->decimal('max_discount_amount', 10, 2)->nullable();
            $table->unsignedInteger('max_total_usage')->default(0);
            $table->unsignedInteger('max_per_user')->default(1);
            $table->date('valid_from');
            $table->date('valid_until');
            $table->enum('target_role', ['all', 'waban', 'umum'])->default('all');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['target_role', 'is_active']);
            $table->index(['valid_from', 'valid_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};