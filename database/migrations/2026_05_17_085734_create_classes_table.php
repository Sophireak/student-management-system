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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->cascadeOnDelete();
            $table->foreignId('grade_id')
                ->constrained('grades')
                ->restrictOnDelete();           // don't allow deleting a grade with classes
            $table->string('name');              // e.g. "1A", "3B"
            $table->integer('capacity')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('academic_year_id');
            $table->index('grade_id');
            $table->unique(['academic_year_id', 'grade_id', 'name']); // no duplicate class names per year+grade
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
