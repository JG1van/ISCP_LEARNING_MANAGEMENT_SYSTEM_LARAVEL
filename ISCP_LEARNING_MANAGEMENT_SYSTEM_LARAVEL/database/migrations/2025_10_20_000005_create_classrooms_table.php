<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->integer('id', false, false)->nullable(false);
            $table->primary('id');
            $table->integer('serial_id')->nullable(false);
            $table->string('name', 100);
            $table->string('grade', 10);
            $table->string('code', 24);
            $table->timestamps();

            $table->foreign('serial_id')->references('id')->on('serials')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
