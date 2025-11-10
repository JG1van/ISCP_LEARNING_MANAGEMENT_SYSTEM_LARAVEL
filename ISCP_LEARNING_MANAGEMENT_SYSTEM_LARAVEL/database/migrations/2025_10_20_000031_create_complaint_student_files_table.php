<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('complaint_student_files', function (Blueprint $table) {
            // id utama pakai integer biar seragam
            $table->integer('id', false, false)->nullable(false);
            $table->primary('id');

            // foreign key disamakan tipe datanya
            $table->integer('complaint_student_detail_id', false, false)->nullable(false);

            $table->string('complaint_file', 255);
            $table->timestamps();

            // relasi foreign key
            $table->foreign('complaint_student_detail_id')
                ->references('id')
                ->on('complaint_student_details')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_student_files');
    }
};
