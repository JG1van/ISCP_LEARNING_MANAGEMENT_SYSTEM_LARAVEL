<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();

            $table->integer('lesson_id')->nullable(false);
            $table->integer('serial_id')->nullable();
            $table->integer('exercise_type_id')->nullable(false);

            $table->string('title', 200)->nullable();
            $table->tinyInteger('is_admin')->default(1);
            $table->timestamps();

            $table->foreign('lesson_id')
                ->references('id')
                ->on('lessons')
                ->onDelete('cascade');

            $table->foreign('serial_id')
                ->references('id')
                ->on('serials')
                ->onDelete('set null');

            $table->foreign('exercise_type_id')
                ->references('id')
                ->on('exercise_types')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
