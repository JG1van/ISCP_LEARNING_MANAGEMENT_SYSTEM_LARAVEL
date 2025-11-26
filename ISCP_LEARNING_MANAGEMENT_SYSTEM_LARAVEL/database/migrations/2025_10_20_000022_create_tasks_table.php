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
        Schema::create('tasks', function (Blueprint $table) {
            // id utama integer agar konsisten
            $table->integer('id')->autoIncrement();

            // foreign key harus sama tipe-nya dengan tabel referensinya
            $table->integer('serial_id', false, false);
            $table->foreign('serial_id')->references('id')->on('serials')->onDelete('cascade');

            $table->integer('post_id', false, false);
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');

            $table->integer('student_id', false, false);
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');

            $table->text('description')->nullable(false);
            $table->text('attachment')->nullable();
            $table->string('point', 3)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
