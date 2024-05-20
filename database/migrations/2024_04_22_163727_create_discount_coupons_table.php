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
        Schema::create('discount_coupons', function (Blueprint $table) {
            $table->id();
            //the coupon code
            $table->string('code');
            //the human readable discount coupon code name
            $table->string('name')->nullable();
            //the description of coupon
            $table->text('description');
            // max uses this coupon
            $table->integer('max_uses')->nullable();
            // how many time user use this  coupon
            $table->integer('max_uses_user')->nullable();
            //the type of coupon (percentage or fixed)
            $table->enum('type',['percentage','fixed'])->default('fixed');
            //the amount of discount based on type
            $table->double('discount_amount')->nullable();
            //the amount of discount based on type
            $table->double('min_amount')->nullable();
            $table->integer('status')->default(1);
            //coupon start
            $table->timestamp('starts_at')->nullable();
            //coupon ends
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_coupons');
    }
};
