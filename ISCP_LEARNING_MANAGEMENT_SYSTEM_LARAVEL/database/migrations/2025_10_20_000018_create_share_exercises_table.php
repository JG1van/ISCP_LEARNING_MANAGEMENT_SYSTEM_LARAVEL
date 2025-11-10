<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('share_exercises', function (Blueprint $table) {

            $table->integer('serial_id')->nullable(false);
            $table->integer('exercise_id')->nullable(false);


            $table->primary(['serial_id', 'exercise_id']);

            $table->foreign('serial_id')->references('id')->on('serials')->onDelete('cascade');
            $table->foreign('exercise_id')->references('id')->on('exercises')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('share_exercises');
    }
};
