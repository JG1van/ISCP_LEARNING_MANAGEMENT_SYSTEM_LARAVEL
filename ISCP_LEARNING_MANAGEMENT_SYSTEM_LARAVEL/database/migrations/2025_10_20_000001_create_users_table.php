<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->integer('id', false, false)->nullable(false);
            $table->primary('id');

            $table->string('name', 100);
            $table->string('username', 100)->unique();
            $table->string('password', 100);
            $table->string('email', 100)->nullable();
            $table->tinyInteger('role');
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('img', 100)->nullable();
            $table->timestamp('login_at')->nullable();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
