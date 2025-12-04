<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PeminjamanBukuResource extends JsonResource
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
            'users_id' => $this->users_id,
            'data_bukus_id' => $this->data_bukus_id,
            'kode_transaksi' => $this->kode_transaksi,
            'tanggal_pengajuan' => $this->tanggal_pengajuan,
            'tanggal_peminjaman' => $this->tanggal_peminjaman,
            'tanggal_jatuh_tempo' => $this->tanggal_jatuh_tempo,
            'status' => $this->status,
            'tanggal_pengembalian' => $this->tanggal_pengembalian,
            'catatan' => $this->catatan,

            'users' => new UserResource($this->whenLoaded('users')),
            'databukus' => new DataBukuResource($this->whenLoaded('databukus')),
        ];
    }
}
