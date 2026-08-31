<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Admin\ParfumController;
use App\Models\Parfum;
use App\Models\KeranjangItem;
use App\Models\Pesanan;
use App\Models\AdminNotification;
use Carbon\Carbon;
use App\Http\Controllers\Pelanggan\KeranjangController;
use App\Http\Controllers\Pelanggan\AlamatController;
use App\Http\Controllers\Admin\PesananController;

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
    $totalPesanan = Pesanan::count();
    $totalPenghasilan = Pesanan::where('status', 'selesai')->sum('total_harga');
    $pesananSelesai = Pesanan::where('status', 'selesai')
    ->orderBy('created_at')
    ->get(['total_harga', 'created_at']);

$now = Carbon::now();

$mingguIni = collect();

$awalMinggu = $now->copy()->startOfWeek(Carbon::MONDAY);

for ($i = 0; $i < 7; $i++) {
    $tanggal = $awalMinggu->copy()->addDays($i);

    $total = $pesananSelesai
        ->filter(fn ($pesanan) =>
            Carbon::parse($pesanan->created_at)->isSameDay($tanggal)
        )
        ->sum('total_harga');

    $mingguIni->push([
        'label' => $tanggal->translatedFormat('D'),
        'value' => $total,
    ]);
}

$bulanIni = collect();

$awalBulan = $now->copy()->startOfMonth();
$akhirBulan = $now->copy()->endOfMonth();

$jumlahMinggu = (int) ceil(
    $awalBulan->diffInDays($akhirBulan) / 7
);

for ($i = 0; $i < $jumlahMinggu; $i++) {

    $mulai = $awalBulan->copy()->addDays($i * 7);
    $selesai = $mulai->copy()->addDays(6);

    if ($selesai->gt($akhirBulan)) {
        $selesai = $akhirBulan->copy();
    }

    $total = $pesananSelesai
        ->filter(function ($pesanan) use ($mulai, $selesai) {
            $tanggal = Carbon::parse($pesanan->created_at);

            return $tanggal->betweenIncluded(
                $mulai->copy()->startOfDay(),
                $selesai->copy()->endOfDay()
            );
        })
        ->sum('total_harga');

    $bulanIni->push([
        'label' => 'Minggu ' . ($i + 1),
        'value' => $total,
    ]);
}

$tahunIni = collect();

for ($i = 1; $i <= 12; $i++) {

    $total = $pesananSelesai
        ->filter(fn ($pesanan) =>
            Carbon::parse($pesanan->created_at)->year === $now->year &&
            Carbon::parse($pesanan->created_at)->month === $i
        )
        ->sum('total_harga');

    $tanggalBulan = Carbon::create(
        $now->year,
        $i,
        1
    );

    $tahunIni->push([
        'label' => $tanggalBulan->translatedFormat('M'),
        'value' => $total,
    ]);
}

$semua = $pesananSelesai
    ->groupBy(fn ($pesanan) =>
        Carbon::parse($pesanan->created_at)->format('Y-m')
    )
    ->map(function ($items, $key) {

        $tanggal = Carbon::createFromFormat(
            'Y-m',
            $key
        );

        return [
            'label' => $tanggal->translatedFormat('M Y'),
            'value' => $items->sum('total_harga'),
        ];
    })
    ->values();

$grafikPenghasilan = [
    'minggu_ini' => $mingguIni,
    'bulan_ini' => $bulanIni,
    'tahun_ini' => $tahunIni,
    'semua' => $semua,
];

    $notifications = AdminNotification::with([
    'pesanan.user:id,name',
    'pesanan.items.parfum:id,nama',
    ])
        ->where('is_read', false)
        ->latest()
        ->get();

    $notificationCount = $notifications->count();

    return Inertia::render('Admin/Dashboard', [
        'authUser' => request()->user(),
        'totalStok' => $totalStok,
        'parfumsTerendah' => $parfumsTerendah,
        'totalPesanan' => $totalPesanan,
        'totalPenghasilan' => $totalPenghasilan,
        'grafikPenghasilan' => $grafikPenghasilan,
        
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

    Route::get('/admin/pesanan', [PesananController::class, 'index'])
    ->name('admin.pesanan');

    Route::put('/admin/pesanan/{pesanan}/status', [PesananController::class, 'updateStatus'])
    ->name('admin.pesanan.status');

    Route::put('/admin/notifications/{notification}/read',[PesananController::class, 'markNotificationAsRead']
    )->name('admin.notifications.read');
});
Route::get('/Pelanggan', function () {

    $parfums = Parfum::orderBy('nama')->get([
        'id',
        'nama',
        'harga',
        'harga_per_ml',
        'stok',
        'kategori',
        'foto',
    ]);

    $cartCount = auth()->check()
        ? KeranjangItem::where('user_id', auth()->id())->count()
        : 0;

    return Inertia::render('Pelanggan/Index', [
        'parfums' => $parfums,
        'authUser' => auth()->user(),
        'cartCount' => $cartCount,
    ]);

})->name('pelanggan');
Route::get('/Pelanggan/Profil', function () {

    $pesanans = request()->user()
        ->pesanans()
        ->with([
            'items.parfum',
            'alamat',
        ])
        ->latest()
        ->get();

    return Inertia::render('Pelanggan/Profil', [
        'authUser' => request()->user(),
        'pesanans' => $pesanans,
    ]);

})->name('pelanggan.profil');
Route::get('/pelanggan/pesan/{parfum}', function (Parfum $parfum) {

    return redirect()->route('pelanggan');

})->middleware('auth')->name('pelanggan.pesan');

Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/super-admin/dashboard', function () {
        return Inertia::render('SuperAdmin/Dashboard');
    })->name('superadmin.dashboard');
    
});

Route::middleware('auth')->group(function () {

    Route::get(
        '/pelanggan/keranjang',
        [KeranjangController::class, 'index']
    )->name('pelanggan.keranjang');

    Route::post(
    '/pelanggan/checkout',
    [KeranjangController::class, 'prepareCheckout']
    )->name('pelanggan.checkout.prepare');

    Route::get(
        '/pelanggan/checkout',
        [KeranjangController::class, 'showCheckout']
    )->name('pelanggan.checkout');

    Route::post(
        '/pelanggan/keranjang/checkout',
        [KeranjangController::class, 'checkout']
    )->name('pelanggan.keranjang.checkout');


    Route::put(
        '/pelanggan/keranjang/{item}',
        [KeranjangController::class, 'updateJumlah']
    )->name('pelanggan.keranjang.update');


    Route::delete(
        '/pelanggan/keranjang/{item}',
        [KeranjangController::class, 'destroy']
    )->name('pelanggan.keranjang.destroy');


    Route::post(
        '/pelanggan/keranjang/{parfum}',
        [KeranjangController::class, 'store']
    )->name('pelanggan.keranjang.store');


    Route::get(
        '/pelanggan/checkout/success/{pesanan}',
        [KeranjangController::class, 'success']
    )->name('pelanggan.checkout.success');

    Route::post(
    '/pelanggan/pesan-sekarang/{parfum}',
    [KeranjangController::class, 'pesanSekarang']
    )->name('pelanggan.pesan.sekarang');

});

Route::middleware('auth')->group(function () {

    Route::get('/pelanggan/alamat', [AlamatController::class, 'create'])
        ->name('pelanggan.alamat');

    Route::post('/pelanggan/alamat', [AlamatController::class, 'store'])
        ->name('pelanggan.alamat.store');

    Route::get('/api/wilayah/regencies/{provinceCode}', [AlamatController::class, 'regencies'])
        ->name('wilayah.regencies');

    Route::get('/api/wilayah/districts/{regencyCode}', [AlamatController::class, 'districts'])
        ->name('wilayah.districts');

    Route::get('/api/wilayah/villages/{districtCode}', [AlamatController::class, 'villages'])
        ->name('wilayah.villages');
});

require __DIR__.'/auth.php';