<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = category::query();
        if ($request->search) {
            $search = strtolower($request->search);
            $query->where('name', 'like', "%{$search}%");
        }
        if ($request->has('sortColumn') && $request->has('order')) {
            $query->orderBy($request->input('sortColumn'), $request->input('order'));
        } else {
            $query->latest();
        }
        $perPage = request()->get('per_page', 8);
        $categoryResource = $query->paginate($perPage)->appends($request->all());
        return Inertia::render('admin/kategori/Index', [
            'categoryResource' => $categoryResource,
            'filters' => $request->only('search', 'column', 'sortColumn', 'order')
        ]);
    }

    /** 
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/kategori/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);
        category::create($validated);

        return redirect()->route('categories.index')->with('message', 'Kategori Berhasil Ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(category $category)
    {
        return Inertia::render('admin/kategori/Edit', [
            'currentCategory' => new CategoryResource($category)

        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);
        $category->update($validated);

        return redirect()->route('categories.index')->with('message', 'Kategori Berhasil Diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(category $category)
    {
        $category->delete();
        return redirect()->back()->with('message', 'Kategori Berhasil Dihapus');
    }
}
