<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('annual_scores', function (Blueprint $table) {
            $table->id();

            $table->foreignId('enrollment_id')
                  ->constrained('enrollments')
                  ->cascadeOnDelete();

            $table->foreignId('academic_year_id')
                  ->constrained('academic_years')
                  ->restrictOnDelete();

            // Pulled from semester_scores — stored here for report stability
            $table->decimal('semester1_avg', 5, 2)->nullable()
                  ->comment('Average from semester 1');
            $table->decimal('semester2_avg', 5, 2)->nullable()
                  ->comment('Average from semester 2');

            // Conduct grades per semester (from moral subject)
            $table->string('semester1_conduct', 30)->nullable();
            $table->string('semester2_conduct', 30)->nullable();

            // Final calculated score
            $table->decimal('final_score', 5, 2)->nullable()
                  ->comment('Average of S1 and S2 averages');

            // Pass/fail and ranking
            $table->boolean('is_passing')->nullable()
                  ->comment('true if final_score >= 50');
            $table->smallInteger('rank')->nullable()
                  ->comment('Class rank by final_score');

            // Admin notes — "Promoted", "Repeat Grade", special circumstances
            $table->string('notes', 255)->nullable();

            // Tracks manual changes
            $table->boolean('is_manual_override')->default(false);

            $table->foreignId('entered_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();

            // One annual record per student per year
            $table->unique(
                ['enrollment_id', 'academic_year_id'],
                'annual_scores_unique'
            );

            $table->index('academic_year_id');
            $table->index('enrollment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annual_scores');
    }
};