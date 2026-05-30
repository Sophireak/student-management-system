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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();
            $table->foreignId('class_id')
                ->constrained('classes')
                ->cascadeOnDelete();
            $table->date('enrolled_at');
            $table->enum('status', ['active', 'transferred', 'dropped'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->unique(['student_id', 'class_id']);     // no duplicate enrollment
            $table->index('class_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
