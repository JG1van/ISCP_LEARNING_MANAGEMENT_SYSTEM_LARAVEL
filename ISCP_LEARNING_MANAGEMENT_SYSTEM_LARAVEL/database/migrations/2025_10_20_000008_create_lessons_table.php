<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->integer('id', false, false)->nullable(false);
            $table->primary('id');

            $table->integer('mapel_id')->nullable(false);
            $table->string('name', 50);
            $table->string('grade', 10);
            $table->integer('semester');
            $table->integer('category')->default(1);
            $table->timestamps();

            $table->foreign('mapel_id')
                ->references('id')
                ->on('mapels')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
