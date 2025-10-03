<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('city_settings', function (Blueprint $table) {
            $table->id();
            $table->string('city_name');
            $table->string('city_hall_name');
            $table->string('address');
            $table->string('ibge_code', 7);
            $table->string('state', 2);
            $table->string('zip_code', 8)->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('mayor_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('city_settings');
    }
};
