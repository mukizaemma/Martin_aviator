<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('payment_timing', 32)->nullable()->after('booking_option');
            $table->string('confirmation_channel', 32)->nullable()->after('payment_timing');

            $table->index('payment_timing');
            $table->index('confirmation_channel');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex(['payment_timing']);
            $table->dropIndex(['confirmation_channel']);
            $table->dropColumn(['payment_timing', 'confirmation_channel']);
        });
    }
};
