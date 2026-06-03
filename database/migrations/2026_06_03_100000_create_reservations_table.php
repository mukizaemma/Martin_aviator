<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->json('line_items')->nullable();
            $table->date('checkin');
            $table->date('checkout');
            $table->unsignedSmallInteger('adults')->default(2);
            $table->unsignedSmallInteger('children')->default(0);
            $table->unsignedSmallInteger('rooms')->default(1);
            $table->unsignedSmallInteger('nights')->default(1);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('names');
            $table->string('phone', 64);
            $table->string('email');
            $table->string('address', 255)->nullable();
            $table->string('status', 32)->default('pending');
            $table->text('description')->nullable();
            $table->string('booking_option', 32);
            $table->boolean('airport_pickup')->default(false);
            $table->boolean('airport_dropoff')->default(false);
            $table->longText('message_body');
            $table->timestamps();

            $table->index('booking_option');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
