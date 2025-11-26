<?php

namespace App\Http\Controllers\Api\Anggota;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataBukuResource;
use App\Http\Resources\PeminjamanBukuResource;
use App\Models\DataBuku;
use App\Models\PeminjamanBuku;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Pest\Support\Str;

class PeminjamanBukuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = DataBuku::with('category');
        if ($request->filled('search_by') && $request->filled('search')) {
            $searchBy = $request->get('search_by');
            $search = $request->get('search');
            $query->where($searchBy, 'like', '%' . $search . '%');
        }
        $perPage = request()->get('per_page', 8);
        $peminjamanbuku = $query->paginate($perPage)->appends($request->all());
        // dd($peminjamanbuku);

        return Inertia::render('anggota/peminjaman/Index', [
            'peminjamanbukuResource' => $peminjamanbuku,
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
            'data_bukus_id' => 'required|exists:data_bukus,id',
            'catatan' => 'nullable|string',
        ]);
        PeminjamanBuku::create([
            'users_id' => auth()->id(),  // diambil dari user yang login
            'kode_transaksi' => 'TRX-' . strtoupper(Str::random(8)), // kode unik
            'data_bukus_id' => $validated['data_bukus_id'],

            'tanggal_peminjaman' => now(),
            'tanggal_jatuh_tempo' => now()->addDays(7), // +7 hari, bisa diganti

            'status' => 'pending', // menunggu persetujuan admin
            'tanggal_pengembalian' => null,

            'catatan' => $validated['catatan'] ?? null,
        ]);
        return back()->with('success', 'Permintaan peminjaman berhasil diajukan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DataBuku $peminjamanbuku)
    {
        $peminjamanbuku->load(['category']);
        // dd($peminjamanbuku);
        return Inertia::render('anggota/peminjaman/Show', [
            'currentPeminjaman' => new DataBukuResource($peminjamanbuku),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PeminjamanBuku $peminjamanBuku)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PeminjamanBuku $peminjamanBuku)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PeminjamanBuku $peminjamanBuku)
    {
        //
    }
}
