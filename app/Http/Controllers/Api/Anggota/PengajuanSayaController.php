<?php

namespace App\Http\Controllers\Api\Anggota;

use App\Http\Controllers\Controller;
use App\Http\Resources\PeminjamanBukuResource;
use App\Models\PeminjamanBuku;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PengajuanSayaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PeminjamanBuku::with('databukus')
            ->where('users_id', auth()->id()) // Hanya data anggota yang sedang login
            ->where('status', '!=', 'dipinjam');
        if ($request->search) {
            $search = strtolower($request->search);
            $column = $request->column;

            if ($column && in_array($column, ['kode_transaksi'])) {

                // Kolom langsung
                $query->where($column, 'like', "%{$search}%");
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
        if ($request->status) {
            $status = $request->status;
            $query->whereHas('databukus', function ($q) use ($status) {
                $q->where('status', $status);
            });
        }
        if ($request->has('sortColumn') && $request->has('order')) {
            $sortColumn = $request->input('sortColumn');
            $order = $request->input('order');
            if ($sortColumn === 'data_bukus_id') {
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
        $PengajuanAnngota = $query->paginate($perPage)->appends($request->all());
        // dd($PengajuanAnngota);
        return Inertia::render('anggota/pengajuansaya/Index', [
            'PengajuanAnngotaResource' => $PengajuanAnngota,
            'filters' => $request->only('search', 'column', 'sortColumn', 'order', 'status')
        ]);
    }
    public function show(PeminjamanBuku $pengajuananggota)
    {
        $pengajuananggota->load(['users', 'databukus']);
        // dd($pengajuananggota);
        return Inertia::render('anggota/pengajuansaya/Show', [
            'currentPengajuan' => new PeminjamanBukuResource($pengajuananggota),
        ]);
    }
    public function destroy(PeminjamanBuku $pengajuananggota)
    {
        // Pastikan hanya anggota yang membuat pengajuan yang dapat membatalkan
        if ($pengajuananggota->users_id !== auth()->id()) {
            return response()->json(['success' => 'Unauthorized'], 403);
        }

        // Hanya pengajuan dengan status 'pending' yang dapat dibatalkan
        if (in_array($pengajuananggota->status, ['dipinjam', 'selesai'])) {
            return response()->json(['success' => 'Only pending requests can be cancelled'], 400);
        }

        $pengajuananggota->delete();

        return redirect()->back()->with(['success' => 'Pengajuan pembatalan berhasil.']);
    }
}
