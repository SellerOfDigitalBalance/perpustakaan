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
                $q->where('status', '!=', 'dipinjam');
            });

        $perPage = request()->get('per_page', 8);
        $PengajuanPeminjamanResource = $query->paginate($perPage)->appends($request->all());
        // dd($PengajuanPeminjamanResource);
        return Inertia::render('admin/pengajuanpeminjaman/Index', [
            'PengajuanPeminjamanResource' => $PengajuanPeminjamanResource,
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
            'status' => 'string|required',
        ]);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
