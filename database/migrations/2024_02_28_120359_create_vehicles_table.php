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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->ulid('id')->index()->unique()->primary();
            $table->softDeletes();
            $table->timestamps();
            $table->string('model');
            $table->string('manufacturer');
            $table->string('chassis_number')->unique();
            $table->string('registration_plate')->unique();
            $table->string('seats');
            $table->string('doors');
            $table->integer('kw');
            $table->enum('transmission', ['manual', 'automatic']);
            $table->enum('fuel_type', ['petrol', 'diesel', 'electric', 'hybrid']);
            $table->date('registration_date');
            $table->date('tuev_valid_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
