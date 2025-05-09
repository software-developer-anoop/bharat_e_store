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
        Schema::create('referral_history', function (Blueprint $table) {
            $table->id();
            $table->integer('referral_customer_id')->nullable();
            $table->integer('referrer_customer_id')->nullable();
            $table->string('referral_code',10)->nullable();
            $table->string('referrer_code',10)->nullable();
            $table->float('points',3,1)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_history');
    }
};
