<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('issue_categories', function (Blueprint $table) {
            $table->integer('id', false, false)->nullable(false);
            $table->primary('id');

            $table->string('issue_category_name', 100);
            $table->enum('level', ['Low', 'Medium', 'High']);
            $table->text('solution_text');
            $table->string('guide_file', 255)->nullable();
            $table->string('guide_video', 255)->nullable();
            $table->enum('issue_category_status', ['Active', 'Inactive']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issue_categories');
    }
};
