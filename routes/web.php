<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Admin\ParfumController;
use App\Models\Parfum;


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

Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/super-admin/dashboard', function () {
        return Inertia::render('SuperAdmin/Dashboard');
    })->name('superadmin.dashboard');
    
});

require __DIR__.'/auth.php';