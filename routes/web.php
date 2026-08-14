<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Admin\ParfumController;
use Illuminate\Support\Facades\Auth;


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
        return Inertia::render('Admin/Dashboard', [
            'authUser' => request()->user(),
        ]);
    })->name('admin.dashboard');


    Route::get('/admin/stok', [ParfumController::class, 'index'])
        ->name('admin.stok');

    Route::post('/admin/stok', [ParfumController::class, 'store'])
        ->name('admin.stok.store');

});

Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/super-admin/dashboard', function () {
        return Inertia::render('SuperAdmin/Dashboard');
    })->name('superadmin.dashboard');
    
});

require __DIR__.'/auth.php';