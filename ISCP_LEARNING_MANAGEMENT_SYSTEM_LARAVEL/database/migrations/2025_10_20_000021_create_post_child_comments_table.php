<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('post_child_comments', function (Blueprint $table) {
            // Gunakan integer agar seragam dengan tabel lain
            $table->integer('id')->autoIncrement();

            $table->integer('post_comment_id')->nullable(false);
            $table->integer('user_id')->nullable();
            $table->integer('student_id')->nullable();

            $table->text('message');
            $table->boolean('is_user');
            $table->timestamps();

            // Foreign key manual agar tipe data cocok
            $table->foreign('post_comment_id')->references('id')->on('post_comments')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_child_comments');
    }
};
