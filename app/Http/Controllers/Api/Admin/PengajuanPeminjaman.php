<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataBukuResource;
use App\Http\Resources\PeminjamanBukuResource;
use App\Models\PeminjamanBuku;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PengajuanPeminjaman extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PeminjamanBuku::with('users', 'databukus')
            ->whereHas('databukus', function ($q) {
                $q->where('status', 'pending');
            });
        if ($request->search) {
            $search = strtolower($request->search);
            $column = $request->column;

            if ($column && in_array($column, ['kode_transaksi'])) {

                // Kolom langsung
                $query->where($column, 'like', "%{$search}%");
            } elseif ($column === 'users_id') {

                // Cari berdasarkan relasi users → name
                $query->whereHas('users', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            } elseif ($column === 'data_bukus_id') {

                // Cari berdasarkan relasi databukus → judul_buku
                $query->whereHas('databukus', function ($q) use ($search) {
                    $q->where('judul_buku', 'like', "%{$search}%");
                });
            } else {
                $query->where(function ($q) use ($search) {
                    $q->orWhere('kode_transaksi', 'like', "%{$search}%")
                        ->orWhereHas('users', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('databukus', function ($q) use ($search) {
                            $q->where('judul_buku', 'like', "%{$search}%");
                        });
                });
            }
        }
        if ($request->has('sortColumn') && $request->has('order')) {
            $sortColumn = $request->input('sortColumn');
            $order = $request->input('order');

            if ($sortColumn === 'users_id') {
                // Sort berdasarkan nama anggota (relasi users)
                $query->join('users', 'peminjaman_bukus.users_id', '=', 'users.id')
                    ->orderBy('users.name', $order)
                    ->select('peminjaman_bukus.*');
            } elseif ($sortColumn === 'data_bukus_id') {
                // Sort berdasarkan nama buku (relasi databukus)
                $query->join('data_bukus', 'peminjaman_bukus.data_bukus_id', '=', 'data_bukus.id')
                    ->orderBy('data_bukus.judul_buku', $order)
                    ->select('peminjaman_bukus.*');
            } elseif ($sortColumn === 'kode_transaksi') {
                $query->orderBy($sortColumn, $order);
            } elseif ($sortColumn === 'status') {
                $query->orderBy($sortColumn, $order);
            }
        } else {
            $query->latest();
        }
        $perPage = request()->get('per_page', 8);
        $PengajuanPeminjamanResource = $query->paginate($perPage)->appends($request->all());
        // dd($PengajuanPeminjamanResource);
        return Inertia::render('admin/pengajuanpeminjaman/Index', [
            'PengajuanPeminjamanResource' => $PengajuanPeminjamanResource,
            'filters' => $request->only('search', 'column', 'sortColumn', 'order')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'status' => 'required',
        ]);
        // dd($validated);
        PeminjamanBuku::where('id', $request->id)->update($validated);
        return redirect()->back()->with('message', 'Status Pengajuan Peminjaman Berhasil Diterima');
    }


    /**
     * Display the specified resource.
     */
    public function show(PeminjamanBuku $pengajuanpeminjaman)
    {
        $pengajuanpeminjaman->load(['users', 'databukus']);
        // dd($pengajuanpeminjaman);
        return Inertia::render('admin/pengajuanpeminjaman/Show', [
            'currentPengajuan' => new PeminjamanBukuResource($pengajuanpeminjaman),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => 'string|required',
        ]);
        PeminjamanBuku::where('id', $request->id)->update($validated);
        return redirect()->back()->with('message', 'Status Pengajuan Peminjaman Berhasil Diterima');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
