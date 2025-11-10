<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('complaint_user_files', function (Blueprint $table) {
            // ID utama manual
            $table->integer('id', false, false)->nullable(false);
            $table->primary('id');

            // Foreign key manual ke complaint_user_details
            $table->integer('complaint_user_detail_id', false, false)->nullable(false);

            // Kolom file
            $table->string('complaint_file', 255);
            $table->timestamps();

            // Definisi relasi foreign key
            $table->foreign('complaint_user_detail_id')
                ->references('id')
                ->on('complaint_user_details')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_user_files');
    }
};
