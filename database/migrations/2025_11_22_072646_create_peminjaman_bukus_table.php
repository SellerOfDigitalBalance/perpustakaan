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
        Schema::create('peminjaman_bukus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('users_id')->constrained('users')->onDelete('cascade');
            $table->string('kode_transaksi')->unique();
            $table->foreignId('data_bukus_id')->constrained('data_bukus')->onDelete('cascade');
            $table->date('tanggal_peminjaman');
            $table->date('tanggal_jatuh_tempo');
            $table->enum('status', ['dipinjam', 'terlambat', 'hilang', 'dikembalikan', 'pending'])->default('dipinjam');
            $table->date('tanggal_pengembalian')->nullable();
            $table->string('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman_bukus');
    }
};
