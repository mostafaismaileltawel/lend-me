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
        Schema::create('contacts', function (Blueprint $table) {
            $table->foreignId('id')->constrained('users')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('image')->nullable();
            $table->double('total_amount')->default(0);
            $table->string('currency_base')->default('EGP');
            $table->string('user_contact_countrycode');
            $table->string('owner_contact_countrycode');
            $table->string('user_contact_mobile'); 
            $table->foreign('user_contact_mobile')->references('phone_number')->on('users')->onDelete('cascade'); 
            $table->string('owner_user_mobile'); 
            $table->foreign('owner_user_mobile')->references('phone_number')->on('users')->onDelete('cascade'); 

            
            $table->unique(['user_contact_mobile','owner_user_mobile']);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
