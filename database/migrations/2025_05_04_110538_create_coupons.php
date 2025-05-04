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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('coupon_title',100)->nullable();
            $table->string('coupon_description',100)->nullable();
            $table->string('coupon_code',100)->nullable();
            $table->enum('coupon_type', ['Fixed', 'Percent'])->nullable();
            $table->float('coupon_value', 10, 2)->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
