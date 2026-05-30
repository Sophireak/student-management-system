<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_scores', function (Blueprint $table) {
            $table->id();

            // Core relationships
            $table->foreignId('enrollment_id')
                ->constrained('enrollments')
                ->cascadeOnDelete();

            $table->foreignId('subject_id')
                ->constrained('subjects')
                ->restrictOnDelete();

            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->restrictOnDelete();

            // Fixed period — month number in Cambodian school year
            // Month 1 = September (start of school year)
            // Month 9 = May (end of school year)
            $table->tinyInteger('month')
                ->unsigned()
                ->comment('1=Sep, 2=Oct, 3=Nov, 4=Dec, 5=Jan, 6=Feb, 7=Mar, 8=Apr, 9=May');

            // Score fields — nullable because not all subjects use all types
            $table->decimal('score', 5, 2)->nullable()
                ->comment('Numeric score for Khmer, Math, Science, Social');

            $table->string('grade', 20)->nullable()
                ->comment('Text grade for Moral: Good/Satisfactory/Needs Improvement');

            $table->string('pass_fail', 10)->nullable()
                ->comment('Pass/Fail for P.Ed, Art, Music');

            // Audit
            $table->foreignId('entered_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('locked_at')->nullable()
                ->comment('Set by admin when month is locked for editing');

            $table->timestamps();

            // One score per student per subject per month per year
            $table->unique(
                ['enrollment_id', 'subject_id', 'month', 'academic_year_id'],
                'monthly_scores_unique'
            );

            // Indexes for report queries
            $table->index(['academic_year_id', 'month']);
            $table->index('enrollment_id');
            $table->index('subject_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_scores');
    }
};
