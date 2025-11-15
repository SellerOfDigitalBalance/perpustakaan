<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();
        // Search
        if ($request->search) {
            $search = strtolower($request->search);
            $column = $request->column;

            if ($column && in_array($column, ['name', 'nik', 'email', 'no_hp'])) {
                $query->where($column, 'like', "%{$search}%");
            } else {
                $query->where(function ($q) use ($search) {
                    $q->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('no_hp', 'like', "%{$search}%");
                });
            }
        }
        // SortColumn $ Order
        if ($request->has('sortColumn') && $request->has('order')) {
            $query->orderBy($request->input('sortColumn'), $request->input('order'));
        } else {
            $query->latest();
        }

        $perPage = request()->get('per_page', 8);
        $userResource = $query->paginate($perPage)->appends($request->all());
        return Inertia::render('admin/user/Index', [
            'userResource' => $userResource,
            'filters' => $request->only('search', 'column', 'sortColumn', 'order')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/user/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // untuk tidak menampilkan error "undefined variable $request "
        /** @var \Illuminate\Http\Request $request */

        $data = $request->validate([
            'nik' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'email_verified_at' => 'nullable|date',
            'password' => 'required|confirmed|min:6',
            'no_hp' => 'nullable|string|max:13',
            'profile_user' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
            'level' => 'required|in:admin,anggota,petugas',
        ]);

        try {
            $name = Str::slug($data['name']);

            // Jika ada file profil diunggah
            if ($request->hasFile('profile_user')) {
                $folderPath = "profile_user/{$name}";
                $file = $request->file('profile_user');
                $filename = $file->getClientOriginalName();
                $data['profile_user'] = $file->storeAs($folderPath, $filename, 'public');
            }

            // Enkripsi password dan verifikasi otomatis
            $data['password'] = Hash::make($data['password']);
            $data['email_verified_at'] = now();

            User::create($data);

            return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return back()->withErrors(['email' => 'Email sudah digunakan'])->withInput();
            }

            return back()->withErrors(['general' => 'Terjadi kesalahan saat menyimpan'])->withInput();
        }
    }

    public function resetPassword(User $user)
    {
        // untuk tidak menampilkan error "undefined variable $user"
        /** @var \App\Models\User $user */


        $user->update([
            'password' => Hash::make(env('DEFAULT_PASSWORD', 'password')),
        ]);

        return back()->with('success', 'Password berhasil direset ke password awal.');
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
    public function edit(User $user)
    {
        // untuk tidak menampilkan error "undefined variable $user"
        /** @var \App\Models\User $user */

        // dd($user);
        return Inertia::render('admin/user/Edit', [
            'currentUser' => new UserResource($user),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // untuk tidak menampilkan error "undefined variable $request dan $user"
        /** @var \Illuminate\Http\Request $request */
        /** @var \App\Models\User $user */

        if (!$request->File('profile_user')) {
            $request->request->remove('profile_user');
        }
        $data = $request->validate([
            'nik' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'email_verified_at' => 'nullable|date',
            'no_hp' => 'nullable|string|max:13',
            'profile_user' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
            'level' => 'required|in:admin,anggota,petugas',
        ]);
        // dd($data);
        try {
            // Menyimpan nama di variabel $name
            $name = Str::slug($data['name']);

            // Jika ada file gambar baru
            if ($request->hasFile('profile_user')) {
                $file = $request->file('profile_user');

                // Backup file lama jika ada
                if ($user->profile_user && Storage::disk('public')->exists($user->profile_user)) {
                    $filename = basename($user->profile_user);
                    $timestamp = now()->format('Ymd_His');
                    $backupPath = "backup/profile_user/{$name}/backup_{$timestamp}_{$filename}";

                    // Pastikan direktori backup ada
                    Storage::disk('public')->makeDirectory("backup/profile_user/{$name}");

                    // Pindahkan file lama ke folder backup
                    Storage::disk('public')->move($user->profile_user, $backupPath);
                }

                // Simpan file baru
                $folderPath = "profile_user/{$name}";
                $filename = $file->getClientOriginalName();
                $data['profile_user'] = $file->storeAs($folderPath, $filename, 'public');
            } else {
                // Jangan update kolom profile_user kalau tidak ada file baru
                unset($data['profile_user']);
            }

            // Enkripsi password jika diisi
            // if (!empty($data['password'])) {
            //     $data['password'] = Hash::make($data['password']);
            // } else {
            //     unset($data['password']);
            // }

            $user->update($data);

            return redirect()->route('users.index')->with('success', 'Pengguna Berhasil Diubah');
        } catch (\Exception $e) {
            if ($e->getCode() === '23000') {
                return back()->withErrors(['email' => 'Email sudah digunakan'])->withInput();
            }
            return back()->withErrors(['general' => 'Terjadi kesalahan saat menyimpan.'])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // untuk tidak menampilkan error "undefined variable $user"
        /** @var \App\Models\User $user */

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
