<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semester_scores', function (Blueprint $table) {
            $table->id();

            $table->foreignId('enrollment_id')
                  ->constrained('enrollments')
                  ->cascadeOnDelete();

            $table->foreignId('subject_id')
                  ->constrained('subjects')
                  ->restrictOnDelete();

            $table->foreignId('academic_year_id')
                  ->constrained('academic_years')
                  ->restrictOnDelete();

            // 1 = Semester 1 (Sep–Jan), 2 = Semester 2 (Feb–May)
            $table->tinyInteger('semester')->unsigned();

            // Numeric score — auto-calculated average of monthly scores
            // Can be manually overridden by admin
            $table->decimal('score', 5, 2)->nullable();

            // Text grades (Moral, PE, Art — not averaged)
            $table->string('grade', 20)->nullable();
            $table->string('pass_fail', 10)->nullable();

            // Calculated summary fields per enrollment per semester
            // Stored here to avoid recalculating on every report load
            $table->decimal('total_score', 7, 2)->nullable()
                  ->comment('Sum of all numeric subject scores');
            $table->decimal('average_score', 5, 2)->nullable()
                  ->comment('Average of numeric subjects only');
            $table->smallInteger('rank')->nullable()
                  ->comment('Class rank based on average');

            // Tracks whether score was auto-calculated or manually set
            $table->boolean('is_manual_override')->default(false);

            $table->foreignId('entered_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();

            // One semester score per student per subject per semester per year
            $table->unique(
                ['enrollment_id', 'subject_id', 'semester', 'academic_year_id'],
                'semester_scores_unique'
            );

            $table->index(['academic_year_id', 'semester']);
            $table->index('enrollment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semester_scores');
    }
};