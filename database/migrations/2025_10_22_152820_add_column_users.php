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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nik')->unique()->after('name');
            $table->string('profile_user')->nullable()->after('nik');
            $table->string('no_hp')->nullable()->after('profile_user');
            $table->enum('level', ['admin', 'petugas', 'anggota'])->default('anggota')->after('no_hp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('nik', 'profile_user', 'no_hp', 'level');
        });
    }
};
