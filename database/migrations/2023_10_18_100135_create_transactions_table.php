<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('from_phone_number');
            $table->string('to_phone_number');
            $table->foreign('from_phone_number')->references('phone_number')->on('users')->onDelete('cascade');
            $table->foreign('to_phone_number')->references('phone_number')->on('users')->onDelete('cascade');
            $table->string('from_country_code');
            $table->string('to_country_code');
            $table->double('amount');
            $table->string('currency_send');
            $table->double('exchange_rate');
            $table->string('currency_base');
            $table->double('amount_exchange');
            $table->enum('status',['refused', 'confirmed']);
            $table->timestamp('send_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('confirm_time')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->unsignedBigInteger('receiver_id');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
       $table->unsignedBigInteger('sender_id');
        $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
        $table->SoftDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
