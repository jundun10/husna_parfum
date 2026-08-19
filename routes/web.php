<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Admin\ParfumController;
use App\Models\Parfum;
use App\Http\Controllers\Pelanggan\KeranjangController;
use App\Http\Controllers\Pelanggan\AlamatController;


Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');


Route::get('/test-vue', function () {
    return Inertia::render('Test');
});

Route::get('/auth/google/redirect', [SocialiteController::class, 'redirectToGoogle'])
    ->name('google.redirect');

Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback'])
    ->name('google.callback');

Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/admin/dashboard', function () {

        $parfumsTerendah = Parfum::orderBy('stok', 'asc')
            ->take(5)
            ->get(['id', 'nama', 'stok']);

        $totalStok = Parfum::sum('stok');

        return Inertia::render('Admin/Dashboard', [
            'authUser' => request()->user(),
            'totalStok' => $totalStok,
            'parfumsTerendah' => $parfumsTerendah,
        ]);

    })->name('admin.dashboard');

    Route::get('/admin/stok', [ParfumController::class, 'index'])
        ->name('admin.stok');

    Route::post('/admin/stok', [ParfumController::class, 'store'])
        ->name('admin.stok.store');
    
    Route::put('/admin/stok/{parfum}', [ParfumController::class, 'update'])
    ->name('admin.stok.update');

    Route::delete('/admin/stok/{parfum}', [ParfumController::class, 'destroy'])
    ->name('admin.stok.destroy');

});
Route::get('/Pelanggan', function () {

    $parfums = Parfum::orderBy('nama')->get([
        'id',
        'nama',
        'harga',
        'stok',
        'kategori',
        'foto',
    ]);

    return Inertia::render('Pelanggan/Index', [
        'parfums' => $parfums,
        'authUser' => auth()->user(),
    ]);

})->name('pelanggan');
Route::get('/pelanggan/pesan/{parfum}', function (Parfum $parfum) {

    return redirect()->route('pelanggan');

})->middleware('auth')->name('pelanggan.pesan');


Route::get('/pelanggan/keranjang', function () {

    return redirect()->route('pelanggan');

})->middleware('auth')->name('pelanggan.keranjang');

Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/super-admin/dashboard', function () {
        return Inertia::render('SuperAdmin/Dashboard');
    })->name('superadmin.dashboard');
    
});
Route::middleware('auth')->group(function () {
    Route::post('/pelanggan/keranjang/{parfum}', [KeranjangController::class, 'store'])
        ->name('pelanggan.keranjang.store');
});
Route::middleware('auth')->group(function () {

    Route::get('/pelanggan/keranjang', [KeranjangController::class, 'index'])
        ->name('pelanggan.keranjang');

    Route::post('/pelanggan/keranjang/{parfum}', [KeranjangController::class, 'store'])
        ->name('pelanggan.keranjang.store');

});
Route::middleware('auth')->group(function () {

    Route::get('/pelanggan/alamat', [AlamatController::class, 'create'])
        ->name('pelanggan.alamat');

    Route::post('/pelanggan/alamat', [AlamatController::class, 'store'])
        ->name('pelanggan.alamat.store');

});

require __DIR__.'/auth.php';