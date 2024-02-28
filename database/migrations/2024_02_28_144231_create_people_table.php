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
        Schema::create('people', function (Blueprint $table) {
            $table->ulid('id')->index()->unique()->primary();
            $table->softDeletes();
            $table->timestamps();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique()->nullable()->default(null);
            $table->string('phone')->unique()->nullable()->default(null);
            $table->date('license_issue_date')->nullable()->default(null);
            $table->date('last_license_check_date')->nullable()->default(null);
            $table->text('notes')->default("");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
