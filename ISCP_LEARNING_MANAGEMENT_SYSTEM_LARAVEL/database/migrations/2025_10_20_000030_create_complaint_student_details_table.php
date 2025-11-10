<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('complaint_student_details', function (Blueprint $table) {
            // ID utama manual
            $table->integer('id', false, false)->nullable(false);
            $table->primary('id');

            // Foreign key manual agar cocok dengan tipe integer di tabel induk
            $table->integer('complaint_message_student_id', false, false)->nullable(false);

            // Kolom lainnya
            $table->enum('message_sender', ['Reporter', 'CS', 'System']);
            $table->text('message_content');
            $table->dateTime('sent_time');
            $table->timestamps();

            // Relasi foreign key
            $table->foreign('complaint_message_student_id')
                ->references('id')
                ->on('complaint_messages_students')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_student_details');
    }
};
