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
        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')
                ->constrained('classes')
                ->cascadeOnDelete();
            $table->foreignId('subject_id')
                ->constrained('subjects')
                ->restrictOnDelete();
            $table->string('name');                 // e.g. "Midterm", "Final Exam Q1"
            $table->enum('term', ['term1', 'term2', 'term3'])->nullable();
            $table->date('exam_date')->nullable();
            $table->decimal('max_score', 5, 2)->default(100.00);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('class_id');
            $table->index('subject_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_sessions');
    }
};
