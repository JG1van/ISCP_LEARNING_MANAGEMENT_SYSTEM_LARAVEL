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
        Schema::create('exercise_points', function (Blueprint $table) {
            // id disamakan dengan tabel lain (integer, bukan bigint)
            $table->integer('id')->autoIncrement();

            // foreign key pakai integer juga
            $table->integer('serial_id')->nullable(false);
            $table->integer('exercise_id')->nullable(false);
            $table->integer('student_id')->nullable(false);

            $table->text('answer');
            $table->text('competence_point')->nullable();
            $table->string('exercise_point', 3)->nullable();
            $table->timestamps();

            // definisi foreign key dengan tipe integer
            $table->foreign('serial_id')->references('id')->on('serials')->onDelete('cascade');
            $table->foreign('exercise_id')->references('id')->on('exercises')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercise_points');
    }
};
