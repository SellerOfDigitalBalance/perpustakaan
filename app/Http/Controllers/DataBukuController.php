<?php

namespace App\Http\Controllers;

use App\Http\Resources\DataBukuResource;
use App\Models\DataBuku;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DataBukuController extends Controller
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
        $dataBuku = $query->paginate($perPage)->appends($request->all());
        return Inertia::render('admin/dataBuku/Index', [
            'dataBukuResource' => $dataBuku,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/dataBuku/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul_buku' => 'required|string|max:255',
            'penulis_buku' => 'required|string|max:255',
            'penerbit_buku' => 'required|string|max:255',
            'categories_id' => 'required|exists:categories,id',
            'tahun_terbit' => 'string|nullable',
            'ISBN' => 'required|string|max:20|unique:data_bukus,ISBN',
            'jumlah_halaman' => 'required|integer|min:1',
            'deskripsi_singkat' => 'nullable|string',
        ]);
        // dd($validated);
        DataBuku::create($validated);

        return redirect()->route('databukus.index')->with('success', 'Data buku berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DataBuku $databuku)
    {
        // dd($databuku);
        return inertia('admin/dataBuku/Show', [
            'currentBook' => new DataBukuResource($databuku),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DataBuku $databuku)
    {
        // dd($databuku);
        return Inertia::render('admin/dataBuku/Edit', [
            'currentBook' => new DataBukuResource($databuku),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DataBuku $databuku)
    {
        $validated = $request->validate([
            'judul_buku' => 'required|string|max:255',
            'penulis_buku' => 'required|string|max:255',
            'penerbit_buku' => 'required|string|max:255',
            'categories_id' => 'required|exists:categories,id',
            'tahun_terbit' => 'string|nullable',
            'ISBN' => 'required|string|max:20|unique:data_bukus,ISBN,' . $databuku->id,
            'jumlah_halaman' => 'required|integer|min:1',
            'deskripsi_singkat' => 'nullable|string',
        ]);

        $databuku->update($validated);

        return redirect()->route('databukus.index')->with('success', 'Data buku berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataBuku $databuku)
    {
        $databuku->delete();
        return redirect()->route('databukus.index')->with('success', 'Data buku berhasil dihapus.');
    }
}
