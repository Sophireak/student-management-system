<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->enum('session_period', ['morning', 'afternoon'])
                  ->default('morning')
                  ->after('capacity');
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn('session_period');
        });
    }
};