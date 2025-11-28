<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Category extends Model
{

    use HasFactory, Notifiable;

    protected $table = 'categories';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    public function dataBukus()
    {
        return $this->hasMany(DataBuku::class, 'categories_id');
    }

    public function peminjamanbukus()
    {
        return $this->hasMany(PeminjamanBuku::class, 'categories_id');
    }
}
