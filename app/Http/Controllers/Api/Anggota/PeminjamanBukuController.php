<?php

namespace App\Http\Controllers\Api\Anggota;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataBukuResource;
use App\Http\Resources\PeminjamanBukuResource;
use App\Models\Category;
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
        if ($request->search) {
            $search = strtolower($request->search);
            $column = $request->column;

            if ($column && in_array($column, ['judul_buku', 'penulis_buku', 'penerbit_buku', 'tahun_terbit'])) {
                $query->where($column, 'like', "%{$search}%");
            } else {

                $query->where(function ($q) use ($search) {
                    $q->orWhere('judul_buku', 'like', "%{$search}%")
                        ->orWhere('penulis_buku', 'like', "%{$search}%")
                        ->orWhere('penerbit_buku', 'like', "%{$search}%")
                        ->orWhere('tahun_terbit', 'like', "%{$search}%");
                });
            }
        }
        // dd($request->category);
        if ($request->category) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }
        if ($request->has('sortColumn') && $request->has('order')) {
            $sortColumn = $request->input('sortColumn');
            $order = $request->input('order');

            if ($sortColumn === 'users_id') {
                $query->join('users_id', 'users.users_id', '=', 'users.id')
                    ->orderBy('users.name', $order)
                    ->select('users.*');
            } elseif ($sortColumn === 'data_bukus_id') {
                $query->join('data_bukus_id', 'databukus.data_bukus_id', '=', 'databukus.id')
                    ->orderBy('databukus.name', $order)
                    ->select('databukus.*');
            } elseif ($sortColumn === 'kode_transaksi') {
                $query->orderBy($sortColumn, $order);
            } elseif ($sortColumn === 'status') {
                $query->orderBy($sortColumn, $order);
            }
        } else {
            $query->latest();
        }
        $perPage = request()->get('per_page', 8);
        $peminjamanbuku = $query->paginate($perPage)->appends($request->all());
        // dd($peminjamanbuku);
        return Inertia::render('anggota/peminjaman/Index', [
            'peminjamanbukuResource' => $peminjamanbuku,
            'all_category_names' => Category::pluck('name')->toArray(),
            'filters' => $request->only('search', 'column', 'sortColumn', 'order', 'category'),
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

            'tanggal_peminjaman' => null,
            'tanggal_jatuh_tempo' => null, // +7 hari, bisa diganti

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
