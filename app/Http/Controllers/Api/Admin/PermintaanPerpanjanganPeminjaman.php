<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeminjamanBuku;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PermintaanPerpanjanganPeminjaman extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PeminjamanBuku::with(['databukus', 'users'])
            ->where('status_perpanjangan', 'pending');
        $perPage = request()->get('per_page', 8);
        $permintaanResource = $query->paginate($perPage)->appends($request->all());
        return Inertia::render('admin/permintaan/Index', [
            'permintaanResource' => $permintaanResource,
            // 'filters' => $request->only('search', 'column', 'sortColumn', 'order', 'level'),
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
            'status_perpanjangan' => 'required',
            'tanggal_jatuh_tempo' => 'nullable',
        ]);
        // dd($validated);

        PeminjamanBuku::where('id', $request->id)->update($validated);

        // Cek status
        if ($validated['status_perpanjangan'] === 'approved') {
            return redirect()
                ->back()   // sesuaikan dengan route yang kamu punya
                ->with('success', 'Pengajuan peminjaman berhasil diterima.');
        }

        if ($validated['status_perpanjangan'] === 'rejected') {
            return redirect()->back() // sesuaikan dengan route kamu
                ->with('error', 'Pengajuan peminjaman dibatalkan.');
        }

        // fallback default
        return redirect()->back()
            ->with('info', 'Status telah diperbarui.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
