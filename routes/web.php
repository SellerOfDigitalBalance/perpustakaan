<?php

use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\DataBukuController;
use App\Http\Controllers\Api\Anggota\PeminjamanBukuController;
use App\Http\Controllers\Api\Admin\PengajuanPeminjaman;
use App\Http\Controllers\PreviewController;
use App\Http\Controllers\SelectCategory;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('admin/Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('/preview/{path}', [PreviewController::class, 'show'])->where('path', '.*');
});

// 🔒 Kelompok route yang hanya bisa diakses oleh pengguna yang login
// Route::middleware(['auth', 'verified'])->group(function () {

//     Route::get('/api/categories', [SelectCategory::class, 'index']);
//     Route::put('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.resetPassword');

// 🔹 Kelompok route khusus admin
// Route::prefix('admin')->group(function () {
// resource otomatis: admin/... (contoh: admin/users)
//     Route::resource('users', UserController::class);
//     Route::resource('categories', CategoryController::class);
//     Route::resource('databukus', DataBukuController::class);
// });

// 🔹 Kelompok route khusus anggota
//     Route::prefix('anggota')->group(function () {
//         Route::resource('peminjamanbukus', PeminjamanBukuController::class);
//     });
// });

Route::middleware(['auth', 'verified'])->group(function () {
    Gate::define('admin', function ($user) {
        return $user->level === 'admin';
    });
    Gate::define('anggota', function ($user) {
        return $user->level === 'anggota';
    });

    Route::get('/api/categories', [SelectCategory::class, 'index']);
    Route::put('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.resetPassword');
    Route::group(['middleware' => ['auth', 'verified', 'can:admin']], function () {
        Route::resource('users', UserController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('databukus', DataBukuController::class);
        Route::resource('pengajuanpeminjamans', PengajuanPeminjaman::class);
    });
    Route::group(['middleware' => ['auth', 'verified', 'can:anggota']], function () {
        Route::resource('peminjamanbukus', PeminjamanBukuController::class);
    });
});

require __DIR__ . '/settings.php';
