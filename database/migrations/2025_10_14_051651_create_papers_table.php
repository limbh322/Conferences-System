<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('papers', function (Blueprint $table) {
            $table->id('paper_id');
            $table->string('title', 150);
            $table->text('abstract')->nullable();
            $table->string('file_path')->nullable();
            $table->string('keywords')->nullable();
            $table->enum('status', ['Submitted', 'Under Review', 'Accepted', 'Rejected'])->default('Submitted');
            $table->unsignedBigInteger('author_id')->nullable();
            $table->unsignedBigInteger('conference_id');
            
            // ✅ Only one timestamps() line
            $table->timestamps();

            // ✅ Foreign keys
            $table->foreign('author_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('conference_id')->references('conference_id')->on('conferences')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('papers');
    }
};
