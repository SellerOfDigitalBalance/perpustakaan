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
        Schema::create('data_bukus', function (Blueprint $table) {
            $table->id();
            $table->string('judul_buku')->unique();
            $table->string('penulis_buku')->unique();
            $table->string('penerbit_buku')->unique();
            $table->string('tahun_terbit');
            $table->foreignId('categories_id')->constrained('categories')->onDelete('cascade');
            $table->string('ISBN')->unique();
            $table->integer('jumlah_halaman');
            $table->string('deskripsi_singkat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_bukus');
    }
};
