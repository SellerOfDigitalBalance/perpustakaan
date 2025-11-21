<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataBuku extends Model
{
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
}
