<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeminjamanBuku extends Model
{
    protected $table = 'peminjaman_bukus';
    protected $fillable = [
        'users_id',
        'kode_transaksi',
        'data_bukus_id',
        'tanggal_pengajuan',
        'tanggal_peminjaman',
        'tanggal_jatuh_tempo',
        'status_perpanjangan',
        'status',
        'tanggal_pengembalian',
        'catatan',
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function databukus()
    {
        return $this->belongsTo(DataBuku::class, 'data_bukus_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'categories_id');
    }
}
