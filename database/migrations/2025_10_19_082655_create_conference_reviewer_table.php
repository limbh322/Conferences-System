<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('conference_reviewer', function (Blueprint $table) {
            $table->id();
            
            // ✅ Foreign key to conferences table (string conference_code)
            $table->string('conference_code');
            
            // ✅ Foreign key to users table
            $table->unsignedBigInteger('reviewer_id');

            // Optional timestamps
            $table->timestamps();

            // ✅ Add foreign key constraints
            $table->foreign('conference_code')
                ->references('conference_code')
                ->on('conferences')
                ->onDelete('cascade');

            $table->foreign('reviewer_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conference_reviewer');
    }
};
