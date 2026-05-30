<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->foreignId('entered_by')
                ->nullable()
                ->after('remarks')
                ->constrained('users')
                ->nullOnDelete();

            // Add subject_id — currently scores only link to exam_session
            // which already has a subject. But the sheet needs subject
            // as a direct filter. We store it denormalized for query speed.
            $table->foreignId('subject_id')
                ->nullable()
                ->after('exam_session_id')
                ->constrained('subjects')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->dropConstrainedForeignId('entered_by');
            $table->dropConstrainedForeignId('subject_id');
        });
    }
};
