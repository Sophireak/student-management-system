<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;



return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semester_report_locks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('class_id')
                ->constrained('classes')
                ->cascadeOnDelete();

            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->restrictOnDelete();

            $table->tinyInteger('semester')->unsigned();

            $table->foreignId('locked_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamp('locked_at');
            $table->timestamps();

            $table->unique(
                ['class_id', 'academic_year_id', 'semester'],
                'semester_lock_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semester_report_locks');
    }
};
