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
            Schema::create('reviews', function (Blueprint $table) {
                $table->id('review_id');
                $table->unsignedBigInteger('paper_id');
                $table->unsignedBigInteger('reviewer_id');
                $table->integer('score')->nullable();
                $table->text('comments')->nullable();
                $table->enum('recommendation', ['Accept', 'Reject', 'Revise'])->nullable();
                $table->timestamps();

                // ✅ Corrected foreign keys
                $table->foreign('paper_id')
                    ->references('paper_id')->on('papers')
                    ->cascadeOnDelete();

                $table->foreign('reviewer_id')
                    ->references('id')->on('users') // fixed
                    ->cascadeOnDelete();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('reviews');
        }
    };
