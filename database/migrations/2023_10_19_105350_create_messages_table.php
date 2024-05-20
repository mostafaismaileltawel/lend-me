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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->text('body');
            $table->enum('type', ['text', 'image', 'voice', 'sticker', 'video','file']);
            $table->string('name')->nullable();
            $table->string('localpath')->nullable();
            $table->string('size')->nullable();
            $table->string('file_path')->nullable();
            $table->string('date')->nullable();
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id');
            $table->string('from_phone_number');
            $table->string('to_phone_number');
            $table->foreign('from_phone_number')->references('phone_number')->on('users')->onDelete('cascade');
            $table->foreign('to_phone_number')->references('user_contact_mobile')->on('contacts')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');   
           });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
