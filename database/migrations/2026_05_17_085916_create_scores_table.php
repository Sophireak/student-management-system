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
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')
                ->constrained('enrollments')
                ->cascadeOnDelete();
            $table->foreignId('exam_session_id')
                ->constrained('exam_sessions')
                ->cascadeOnDelete();
            $table->decimal('score', 5, 2);
            $table->text('remarks')->nullable();
            $table->timestamps();

            // Indexes
            $table->unique(['enrollment_id', 'exam_session_id']); // one score per student per exam
            $table->index('exam_session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};
