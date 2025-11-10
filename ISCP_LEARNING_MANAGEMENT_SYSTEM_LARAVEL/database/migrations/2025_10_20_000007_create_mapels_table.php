<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mapels', function (Blueprint $table) {
            $table->integer('id', false, false)->nullable(false);
            $table->primary('id');
            $table->string('name', 20);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mapels');
    }
};
