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
        Schema::create('exercise_items', function (Blueprint $table) {
            // Gunakan integer() agar sama dengan tabel lain (bukan bigint)
            $table->integer('id')->autoIncrement();

            $table->integer('admin_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('competence_id')->nullable();
            $table->integer('exercise_id')->nullable(false);
            $table->integer('exercise_type_id')->nullable(false);
            $table->integer('exercise_model_id')->nullable(false);

            $table->tinyInteger('exercise_choice');
            $table->integer('exercise_number');
            $table->text('question');
            $table->text('selection')->nullable();
            $table->text('answer')->nullable();
            $table->tinyInteger('is_user');
            $table->timestamps();

            // Definisi foreign key disesuaikan agar tidak konflik tipe
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('competence_id')->references('id')->on('competences')->onDelete('set null');
            $table->foreign('exercise_id')->references('id')->on('exercises')->onDelete('cascade');
            $table->foreign('exercise_type_id')->references('id')->on('exercise_types')->onDelete('cascade');
            $table->foreign('exercise_model_id')->references('id')->on('exercise_models')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercise_items');
    }
};
