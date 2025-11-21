<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DataBukuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'judul_buku' => $this->judul_buku,
            'penulis_buku' => $this->penulis_buku,
            'penerbit_buku' => $this->penerbit_buku,
            'tahun_terbit' => $this->tahun_terbit,
            'categories_id' => $this->categories_id,
            'ISBN' => $this->ISBN,
            'jumlah_halaman' => $this->jumlah_halaman,
            'deskripsi_singkat' => $this->deskripsi_singkat,
        ];
    }
}
