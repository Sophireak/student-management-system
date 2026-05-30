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
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')
                ->constrained('classes')
                ->cascadeOnDelete();
            $table->foreignId('subject_id')
                ->constrained('subjects')
                ->restrictOnDelete();
            $table->date('session_date');
            $table->enum('period', ['morning', 'afternoon'])->nullable();
            $table->string('topic')->nullable();        // lesson topic for that session
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('class_id');
            $table->index('session_date');
            $table->unique(['class_id', 'subject_id', 'session_date', 'period'], 'att_sessions_unique'); // no duplicate sessions
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_sessions');
    }
};
