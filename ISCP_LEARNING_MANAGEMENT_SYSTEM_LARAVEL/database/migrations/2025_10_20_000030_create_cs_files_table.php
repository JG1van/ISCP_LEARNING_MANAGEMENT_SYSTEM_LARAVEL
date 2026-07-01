<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('cs_files', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();

            // FK ke cs_rooms
            $table->integer('room_id');

            // Path file
            $table->string('file_path', 255);

            $table->timestamps();

            // FOREIGN KEY
            $table->foreign('room_id')
                ->references('id')
                ->on('cs_rooms')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cs_files');
    }
};
