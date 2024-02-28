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
        Schema::create('vehicle_briefings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->date('issue_date');
            $table->ulid('vehicle_id')->nullable()->default(null);
            $table->foreign('vehicle_id')
                ->references('id')
                ->on('vehicles')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->ulid('person_id')->nullable()->default(null);
            $table->foreign('person_id')
                ->references('id')
                ->on('vehicles')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->ulid('issuer_id')->nullable()->default(null);
            $table->foreign('issuer_id')
                ->references('id')
                ->on('people')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_briefings');
    }
};
