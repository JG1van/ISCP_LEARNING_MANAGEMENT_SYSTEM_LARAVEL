<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('helps', function (Blueprint $table) {
            $table->integer('id', false, false)->nullable(false);
            $table->primary('id');

            $table->string('title', 255);
            $table->text('description');
            $table->integer('priority')->default(3);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('helps');
    }
};
