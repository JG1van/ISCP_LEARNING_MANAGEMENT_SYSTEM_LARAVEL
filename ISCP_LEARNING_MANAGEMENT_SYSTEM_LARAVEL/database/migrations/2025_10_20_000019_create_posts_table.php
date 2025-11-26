<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('serial_id')->nullable(false);
            $table->integer('user_id')->nullable(false);
            $table->integer('mapel_id')->nullable(false);
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->string('slug', 200)->unique();
            $table->text('link')->nullable();
            $table->text('attachment')->nullable();
            $table->text('embed')->nullable();
            $table->text('category')->nullable();
            $table->boolean('is_task')->default(0);
            $table->timestamps();
            $table->foreign('serial_id')->references('id')->on('serials')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('mapel_id')->references('id')->on('mapels')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
