<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeminjamanBuku;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PengembalianBukuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PeminjamanBuku::with(['databukus', 'users'])
            ->where('status', 'dipinjam');
        $perPage = request()->get('per_page', 8);
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
            } elseif ($column === 'judul_buku') {
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
            // dd($sortColumn);
            if (in_array($sortColumn, ['users_id', 'judul_buku', 'kode_transaksi'])) {
                if ($sortColumn === 'users_id') {
                    // Sort berdasarkan nama anggota (relasi users)
                    $query->join('users', 'peminjaman_bukus.users_id', '=', 'users.id')
                        ->orderBy('users.name', $order)
                        ->select('peminjaman_bukus.*');
                } elseif ($sortColumn === 'judul_buku') {
                    // Sort berdasarkan nama buku (relasi databukus)
                    $query->join('data_bukus', 'peminjaman_bukus.data_bukus_id', '=', 'data_bukus.id')
                        ->orderBy('data_bukus.judul_buku', $order)
                        ->select('peminjaman_bukus.*');
                } elseif ($sortColumn === 'kode_transaksi') {
                    $query->orderBy($sortColumn, $order);
                }
            } else {
                // Jika sortColumn tidak valid, bisa diberi default atau error handling
                // Misalnya, urutkan berdasarkan kolom 'kode_transaksi'
                $query->orderBy('kode_transaksi', 'asc');
            }
        } else {
            $query->latest();
        }
        $pengembalianResource = $query->paginate($perPage)->appends($request->all());
        return Inertia::render('admin/pengembalian/Index', [
            'pengembalianResource' => $pengembalianResource,
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
            'status' => 'required',
            'tabbgal_pengembalian' => 'nullable',
        ]);
        // dd($validated);

        PeminjamanBuku::where('id', $request->id)->update($validated);

        // Cek status
        if ($validated['status'] === 'dikembalikan') {
            return redirect()
                ->back()   // sesuaikan dengan route yang kamu punya
                ->with('success', 'Penegmbalian buku berhasil dikembalikan.');
        }

        if ($validated['status'] === 'hilang') {
            return redirect()->back() // sesuaikan dengan route kamu
                ->with('error', 'Pengembalian buku hilang.');
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
