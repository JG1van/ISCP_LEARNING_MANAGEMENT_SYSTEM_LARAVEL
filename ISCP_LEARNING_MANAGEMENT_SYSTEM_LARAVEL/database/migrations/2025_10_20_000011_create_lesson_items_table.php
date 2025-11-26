<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lesson_items', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('lesson_id')->nullable(false);
            $table->integer('theme_id')->nullable(false);
            $table->integer('subtheme_id')->nullable(false);
            $table->integer('admin_id')->nullable(false);
            $table->integer('number');
            $table->text('title');
            $table->text('embed');
            $table->timestamps();

            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
            $table->foreign('theme_id')->references('id')->on('themes')->onDelete('cascade');
            $table->foreign('subtheme_id')->references('id')->on('subthemes')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_items');
    }
};
