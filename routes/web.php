<?php

use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\DataBukuController;
use App\Http\Controllers\PreviewController;
use App\Http\Controllers\SelectCategory;
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
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/api/categories', [SelectCategory::class, 'index']);

    // 🔹 Kelompok route khusus admin
    Route::prefix('admin')->group(function () {
        // resource otomatis: admin/... (contoh: admin/users)
        Route::resource('users', UserController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('databukus', DataBukuController::class);
    });
});

require __DIR__ . '/settings.php';
