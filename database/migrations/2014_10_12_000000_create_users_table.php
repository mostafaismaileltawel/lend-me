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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('country_code');
            $table->string('phone_number')->unique();
            $table->boolean('phone_verified')->default(false);
            $table->integer('verification_code')->unique();
            $table->integer('borrow_amount')->default(0);
            $table->integer('lend_amount')->default(0);
            $table->timestamp('expire_at')->nullable();
            $table->string('image')->nullable();
            $table->string('token_device')->nullable();
            $table->text('token')->nullable();
            $table->SoftDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
