<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('cs_messages', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();

            $table->integer('cs_rooms_id');

            $table->enum('message_sender', ['Pelapor', 'Admin', 'Sistem']);

            $table->text('message_content')->nullable();
            $table->dateTime('sent_time')->nullable();

            $table->timestamps();
            $table->foreign('cs_rooms_id')
                ->references('id')
                ->on('cs_rooms')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cs_messages');
    }

};
