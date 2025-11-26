<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('complaint_categories', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();

            $table->string('name', 100);
            $table->enum('level', ['Umum', 'Siswa', 'Guru']);

            $table->text('solution_text')->nullable();
            $table->text('guide_file')->nullable();
            $table->text('guide_video')->nullable();

            $table->enum('category_status', ['Aktif', 'Tidak Aktif'])->default('Aktif');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('complaint_categories');
    }

};
