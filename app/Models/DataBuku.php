<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class DataBuku extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'data_bukus';

    protected $fillable = [
        'judul_buku',
        'penulis_buku',
        'penerbit_buku',
        'tahun_terbit',
        'categories_id',
        'ISBN',
        'jumlah_halaman',
        'deskripsi_singkat',
    ];

    public function category()
    {
        return $this->belongsTo(category::class, 'categories_id');
    }

    public function peminjaman()
    {
        return $this->hasMany(PeminjamanBuku::class, 'data_bukus_id');
    }
}
