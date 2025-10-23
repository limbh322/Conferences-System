<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Make 'id' auto-increment without redefining primary key
        DB::statement('ALTER TABLE `notifications` MODIFY `id` INT NOT NULL AUTO_INCREMENT;');
    }

    public function down(): void
    {
        // Revert back to non-auto-increment if needed
        DB::statement('ALTER TABLE `notifications` MODIFY `id` INT NOT NULL;');
    }
};
    