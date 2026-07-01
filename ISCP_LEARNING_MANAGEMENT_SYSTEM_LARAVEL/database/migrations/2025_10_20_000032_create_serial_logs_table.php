<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('serial_logs', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('serial_id');
            $table->string('active', 3);
            $table->enum('status', ['Baru', 'Perpanjang'])->default('Baru');
            $table->timestamps();

            $table->foreign('serial_id')
                ->references('id')->on('serials')
                ->cascadeOnDelete();
        });

    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('serial_logs');
    }
};
