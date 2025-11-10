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
        Schema::create('admins', function (Blueprint $table) {
            $table->integer('id', false, false)->nullable(false);
            $table->primary('id');

            $table->string('name', 100);
            $table->string('username', 50)->unique();
            $table->string('password', 100);
            $table->tinyInteger('role');
            $table->string('date_in', 50)->nullable();
            $table->string('position', 20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('img', 100)->nullable();
            $table->timestamp('login_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
