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
        Schema::create('country', function (Blueprint $table) {
            $table->id();
            $table->string('country_name',100)->nullable();
            $table->string('country_code',10)->nullable();
            $table->string('flag_image',100)->nullable();
            $table->string('flag_image_webp',100)->nullable();
            $table->string('flag_image_alt',100)->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country');
    }
};
