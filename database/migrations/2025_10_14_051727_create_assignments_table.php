<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id('assignment_id');
            $table->unsignedBigInteger('paper_id');
            $table->unsignedBigInteger('reviewer_id');
            $table->timestamps();

            // Correct foreign keys
            $table->foreign('paper_id')
                  ->references('paper_id')->on('papers')
                  ->cascadeOnDelete();

            $table->foreign('reviewer_id')
                  ->references('id')->on('users') // fixed
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
