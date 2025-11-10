<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('complaint_history_students', function (Blueprint $table) {
            // Gunakan integer manual sebagai primary key
            $table->integer('id', false, false)->nullable(false);
            $table->primary('id');

            // Semua foreign key juga pakai integer agar konsisten
            $table->integer('issue_category_id', false, false)->nullable(false);
            $table->integer('reporter_student_id', false, false)->nullable(false);
            $table->integer('admin_id', false, false)->nullable(false);

            $table->dateTime('completion_time');
            $table->enum('resolution_by', ['CS', 'System']);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Relasi Foreign Key
            $table->foreign('issue_category_id')
                ->references('id')
                ->on('issue_categories')
                ->onDelete('cascade');

            $table->foreign('reporter_student_id')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');

            $table->foreign('admin_id')
                ->references('id')
                ->on('admins')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_history_students');
    }
};
