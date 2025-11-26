<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('complaint_messages', function (Blueprint $table) {
                  $table->integer('id')->autoIncrement();

            $table->integer('complaint_room_id');

            $table->enum('message_sender', ['Pelapor', 'Admin', 'System']);

            $table->text('message_content')->nullable();
            $table->dateTime('sent_time')->nullable();

            $table->timestamps();
            $table->foreign('complaint_room_id')
                ->references('id')
                ->on('complaint_rooms')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('complaint_messages');
    }

};
