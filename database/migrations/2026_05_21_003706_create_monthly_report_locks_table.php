<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tracks which class+month combinations are locked by admin
// Prevents teachers editing after admin has reviewed

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_report_locks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('class_id')
                ->constrained('classes')
                ->cascadeOnDelete();

            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->restrictOnDelete();

            $table->tinyInteger('month')->unsigned();

            $table->foreignId('locked_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamp('locked_at');
            $table->timestamps();

            $table->unique(
                ['class_id', 'academic_year_id', 'month'],
                'report_lock_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_report_locks');
    }
};
