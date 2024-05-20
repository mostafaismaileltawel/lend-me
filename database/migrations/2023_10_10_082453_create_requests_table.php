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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('body');
            $table->string('type');
            $table->string('from_user_mobile'); 
            $table->foreign('from_user_mobile')->references('phone_number')->on('users')->onDelete('cascade'); 
            $table->string('to_user_mobile'); 
            $table->foreign('to_user_mobile')->references('phone_number')->on('users')->onDelete('cascade'); 
            $table->string('from_country_code');
            $table->string('to_country_code');
            $table->string('status')->nullable();
            $table->double("amount")->nullable();
            $table->timestamp('send_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('edited_time')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')); 
            $table->unsignedBigInteger('sender_id');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');  
            $table->unsignedBigInteger('receiver_id');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade'); 
            $table->string('currency')->nullable(); 
            $table->string('currency_base')->default('EGP'); 
            $table->double('exchange_rate')->nullable(); ;
            $table->double('amount_exchange')->nullable();  });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
