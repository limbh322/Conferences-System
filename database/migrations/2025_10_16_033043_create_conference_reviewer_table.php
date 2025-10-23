<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('conference_reviewer', function (Blueprint $table) {
            $table->id();

            // link by conference_code (not id)
            $table->string('conference_code');
            $table->foreign('conference_code')
                  ->references('conference_code')
                  ->on('conferences')
                  ->onDelete('cascade');

            // store reviewer_name (string, not foreign key)
            $table->string('reviewer_name');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conference_reviewer');
    }
};
