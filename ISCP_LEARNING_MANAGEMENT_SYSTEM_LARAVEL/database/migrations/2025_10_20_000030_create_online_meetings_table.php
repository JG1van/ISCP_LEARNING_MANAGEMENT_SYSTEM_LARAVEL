<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('online_meetings', function (Blueprint $table) {
            // ID manual (tanpa auto increment)
            $table->integer('id')->autoIncrement();

            // Relasi
            $table->integer('serial_id')->unsigned();
            $table->integer('classroom_id')->unsigned();
            $table->integer('user_id')->unsigned();

            // Data utama
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->string('meeting_code', 50);
            $table->text('meeting_link');
            $table->string('platform', 50)->nullable(); // Zoom, Meet, Webex, dll

            // Waktu
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();

            // Status
            $table->enum('status', ['upcoming', 'live', 'ended', 'cancelled'])->default('upcoming');

            // Tambahan Laravel
            $table->timestamps();

            // Index
            $table->index(['serial_id', 'classroom_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('online_meetings');
    }

};
