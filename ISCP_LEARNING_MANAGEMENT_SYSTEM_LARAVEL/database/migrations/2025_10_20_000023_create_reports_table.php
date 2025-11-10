<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            // ID manual, tanpa auto increment
            $table->integer('id', false, false)->nullable(false);
            $table->primary('id');

            $table->integer('serial_id')->nullable(false);
            $table->integer('student_id')->nullable(false);
            $table->text('report');
            $table->string('img', 50)->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('serial_id')
                ->references('id')
                ->on('serials')
                ->cascadeOnDelete();

            $table->foreign('student_id')
                ->references('id')
                ->on('students')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
