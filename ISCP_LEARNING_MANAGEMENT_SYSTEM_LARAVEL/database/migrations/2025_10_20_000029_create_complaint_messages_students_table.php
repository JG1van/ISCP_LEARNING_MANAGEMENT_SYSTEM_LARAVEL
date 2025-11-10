<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('complaint_messages_students', function (Blueprint $table) {
            // ID utama manual
            $table->integer('id', false, false)->nullable(false);
            $table->primary('id');

            // Foreign keys manual agar tipe data cocok
            $table->integer('reporter_student_id', false, false)->nullable(false);
            $table->integer('admin_id', false, false)->nullable(false);
            $table->integer('issue_category_id', false, false)->nullable(false);

            // Field lain
            $table->dateTime('report_time');
            $table->enum('chat_status', ['Pending', 'System', 'CS', 'Completed']);
            $table->timestamps();

            // Definisi relasi manual
            $table->foreign('reporter_student_id')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');

            $table->foreign('admin_id')
                ->references('id')
                ->on('admins')
                ->onDelete('cascade');

            $table->foreign('issue_category_id')
                ->references('id')
                ->on('issue_categories')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_messages_students');
    }
};
