<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('serials', function (Blueprint $table) {
            $table->integer('id', false, false)->nullable(false);
            $table->primary('id');
            $table->integer('user_id')->nullable();
            $table->integer('product_id')->nullable(false);
            $table->string('serial', 50)->unique();
            $table->string('paket', 1);
            $table->string('active', 3);
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('serials');
    }
};
