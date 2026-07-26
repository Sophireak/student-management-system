<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nationality')->nullable()->after('phone');
            $table->string('ethnicity')->nullable()->after('nationality');
            $table->string('birth_place')->nullable()->after('ethnicity');
            $table->text('current_address')->nullable()->after('birth_place');
            $table->enum('gender', ['male', 'female'])->nullable()->after('current_address');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nationality', 'ethnicity', 'birth_place', 'current_address', 'gender']);
        });
    }
};