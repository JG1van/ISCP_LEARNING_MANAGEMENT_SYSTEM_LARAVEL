<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subthemes', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('lesson_id')->nullable(false);
            $table->integer('theme_id')->nullable(false);
            $table->integer('subtheme');
            $table->string('name', 200);

            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
            $table->foreign('theme_id')->references('id')->on('themes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subthemes');
    }
};
