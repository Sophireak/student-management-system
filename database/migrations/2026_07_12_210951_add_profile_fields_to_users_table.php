<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('email');
            $table->string('avatar')->nullable()->after('username');
            $table->date('date_of_birth')->nullable()->after('avatar');
            $table->string('phone', 20)->nullable()->after('date_of_birth');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'avatar', 'date_of_birth', 'phone']);
        });
    }
};