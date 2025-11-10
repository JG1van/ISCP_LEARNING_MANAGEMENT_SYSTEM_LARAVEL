<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->integer('id', false, false)->nullable(false);
            $table->primary('id');

            $table->integer('serial_id')->nullable(false);
            $table->integer('user_id')->nullable(false);
            $table->integer('classroom_id')->nullable(false);

            $table->string('name', 200);
            $table->string('username', 100);
            $table->string('password', 150);
            $table->string('password_text', 100);
            $table->string('nis', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->timestamps();

            $table->foreign('serial_id')->references('id')->on('serials')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('classroom_id')->references('id')->on('classrooms')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
