<?php

namespace App\Http\Controllers\Api\Anggota;

use App\Http\Controllers\Controller;
use App\Http\Resources\PeminjamanBukuResource;
use App\Models\Category;
use App\Models\DataBuku;
use App\Models\PeminjamanBuku;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DaftarPeminjamanBukuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $query = PeminjamanBuku::with(['databukus'])->where('status', 'dipinjam')->get();
        $query = PeminjamanBuku::with('databukus', 'databukus.category')
            ->where('users_id', auth()->id())
            ->where('status', 'dipinjam');
        if ($request->search) {
            $search = strtolower($request->search);
            $column = $request->column;

            if (in_array($column, ['judul_buku', 'penulis_buku', 'penerbit_buku', 'tahun_terbit'])) {
                $query->whereHas('databukus', function ($q) use ($search, $column) {
                    $q->where($column, 'like', "%{$search}%");
                });
            }
        }

        if ($request->has(['sortColumn', 'order'])) {
            $sortColumn = $request->input('sortColumn');
            $order = $request->input('order');

            // kolom yang ada di relasi data_bukus
            $dataBukuColumns = ['judul_buku', 'penulis_buku', 'penerbit_buku', 'tahun_terbit'];

            if (in_array($sortColumn, $dataBukuColumns)) {

                $query->join('data_bukus', 'peminjaman_bukus.data_bukus_id', '=', 'data_bukus.id')
                    ->orderBy("data_bukus.$sortColumn", $order)
                    ->select('peminjaman_bukus.*');
            } elseif (in_array($sortColumn, ['kode_transaksi', 'status'])) {

                $query->orderBy($sortColumn, $order);
            } elseif ($sortColumn === 'jatuh_tempo') {

                // SORTING jatuh_tempo (selisih tanggal)
                $query->orderByRaw(
                    "DATEDIFF(tanggal_jatuh_tempo, tanggal_peminjaman) $order"
                );
            }
        } else {
            $query->latest();
        }


        $perPage = request()->get('per_page', 8);
        $daftar = $query->paginate($perPage)->appends($request->all());
        return Inertia::render('anggota/daftarpeminjaman/Index', [
            'daftarpeminjamanResource' => $daftar,
            // 'all_category_names' => Category::pluck('name')->toArray(),
            'filters' => $request->only('search', 'column', 'sortColumn', 'order', 'category', 'status'),
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
        $request->validate([
            'data_bukus_id' => 'required|exists:peminjaman_bukus,data_bukus_id',
        ]);

        $peminjaman = PeminjamanBuku::where('data_bukus_id', $request->data_bukus_id)
            ->where('status', 'dipinjam') // opsional: hanya peminjaman aktif
            ->first();

        if (!$peminjaman) {
            return back()->with(['error' => 'Data peminjaman tidak ditemukan.']);
        }

        if ($peminjaman->status_perpanjangan === 'pending') {
            return back()->with(['error' => 'Perpanjangan sudah diajukan sebelumnya.']);
        }
        // Set status perpanjangan ke pending
        $peminjaman->update([
            'status_perpanjangan' => 'pending',
        ]);

        return back()->with('success', 'Pengajuan perpanjangan berhasil dikirim ke admin.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PeminjamanBuku $daftarpeminjaman)
    {
        $daftarpeminjaman->load(['databukus', 'databukus.category']);
        // dd($daftarpeminjaman);
        return Inertia::render('anggota/daftarpeminjaman/Show', [
            'currentPengajuan' => new PeminjamanBukuResource($daftarpeminjaman),
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
