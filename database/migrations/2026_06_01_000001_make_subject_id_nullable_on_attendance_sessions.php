<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_sessions', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['subject_id']);

            // Make subject_id nullable
            $table->foreignId('subject_id')
                ->nullable()
                ->change()
                ->constrained('subjects')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);

            $table->foreignId('subject_id')
                ->nullable(false)
                ->change()
                ->constrained('subjects')
                ->restrictOnDelete();
        });
    }
};