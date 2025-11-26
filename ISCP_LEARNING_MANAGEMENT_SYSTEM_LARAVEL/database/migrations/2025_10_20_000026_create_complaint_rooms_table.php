<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('complaint_rooms', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();

            $table->string('complaint_code', 50)->unique();

            // FK ke complaint_categories
            $table->integer('complaint_category_id')->nullable();

            // FK ke students dan teachers
            $table->integer('student_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('admin_id')->nullable();

            $table->enum('chat_status', ['Sistem', 'Admin'])
                ->default('Sistem');

            $table->timestamps();



            $table->foreign('complaint_category_id')
                ->references('id')->on('complaint_categories')
                ->onDelete('set null');

            $table->foreign('student_id')
                ->references('id')->on('students')
                ->onDelete('set null');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('set null');

            $table->foreign('admin_id')
                ->references('id')->on('admins')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('complaint_rooms');
    }
};
