<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\KeranjangItem;
use App\Models\Parfum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Pesanan;
use App\Models\PesananItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\AdminNotification;
use Midtrans\Config;
use Midtrans\Snap;


class KeranjangController extends Controller
{
    public function store(
    Request $request,
    Parfum $parfum
): RedirectResponse {
    if ($parfum->stok <= 0) {
        return back()->with(
            'error',
            'Stok parfum sedang habis.'
        );
    }

    $validated = $request->validate([
        'ukuran_ml' => [
            'required',
            'integer',
            'in:1,5,10,15,20,25,50',
        ],

        'jumlah' => [
            'required',
            'integer',
            'min:1',
        ],
    ]);

    if ($validated['jumlah'] > $parfum->stok) {
        return back()->with(
            'error',
            'Jumlah botol melebihi stok yang tersedia.'
        );
    }

    $item = KeranjangItem::where('user_id', $request->user()->id)
        ->where('parfum_id', $parfum->id)
        ->where('ukuran_ml', $validated['ukuran_ml'])
        ->first();

    if ($item) {
        $jumlahBaru = $item->jumlah + $validated['jumlah'];

        if ($jumlahBaru > $parfum->stok) {
            return back()->with(
                'error',
                'Jumlah botol melebihi stok yang tersedia.'
            );
        }

        $item->update([
            'jumlah' => $jumlahBaru,
        ]);
    } else {
        KeranjangItem::create([
            'user_id' => $request->user()->id,
            'parfum_id' => $parfum->id,
            'ukuran_ml' => $validated['ukuran_ml'],
            'jumlah' => $validated['jumlah'],
        ]);
    }

    return back()->with(
        'success',
        "{$parfum->nama} berhasil dimasukkan ke keranjang."
    );
}
    public function index(Request $request)
{
    $items = KeranjangItem::with('parfum')
    ->where('user_id', $request->user()->id)
    ->get();

$alamat = $request->user()->alamat;

    return Inertia::render('Pelanggan/Keranjang', [
    'items' => $items,
    'alamat' => $alamat,
    'authUser' => $request->user(),
    ]);
}
    public function updateJumlah(
    Request $request,
    KeranjangItem $item
): RedirectResponse {
    if ($item->user_id !== $request->user()->id) {
        abort(403);
    }

    $validated = $request->validate([
        'jumlah' => ['required', 'integer', 'min:1'],
    ]);

    if ($validated['jumlah'] > $item->parfum->stok) {
        return back()->with(
            'error',
            'Jumlah melebihi stok yang tersedia.'
        );
    }

    $item->update([
        'jumlah' => $validated['jumlah'],
    ]);

    return back()->with(
        'success',
        'Jumlah produk berhasil diperbarui.'
    );
}
    public function destroy(
    Request $request,
    KeranjangItem $item
): RedirectResponse {
    if ($item->user_id !== $request->user()->id) {
        abort(403);
    }

    $item->delete();

    return back()->with(
        'success',
        'Produk berhasil dihapus dari keranjang.'
    );
}
public function prepareCheckout(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'item_ids' => ['required', 'array', 'min:1'],
        'item_ids.*' => ['integer'],
    ]);

    $items = KeranjangItem::where('user_id', $request->user()->id)
        ->whereIn('id', $validated['item_ids'])
        ->pluck('id')
        ->toArray();

    if (empty($items)) {
        return back()->with(
            'error',
            'Pilih minimal satu produk untuk checkout.'
        );
    }

    session([
        'checkout_item_ids' => $items,
    ]);

    return redirect()->route('pelanggan.checkout');
}

public function showCheckout(Request $request)
{
    $directCheckout = session('direct_checkout');

    if ($directCheckout) {

        $parfum = Parfum::find($directCheckout['parfum_id']);

        if (!$parfum) {
            session()->forget('direct_checkout');

            return redirect()
                ->route('pelanggan.keranjang')
                ->with('error', 'Produk checkout tidak ditemukan.');
        }

        $item = new KeranjangItem([
            'id' => null,
            'user_id' => $request->user()->id,
            'parfum_id' => $parfum->id,
            'ukuran_ml' => $directCheckout['ukuran_ml'],
            'jumlah' => $directCheckout['jumlah'],
        ]);

        $item->setRelation('parfum', $parfum);

        $items = collect([$item]);

    } else {

        $itemIds = session('checkout_item_ids', []);

        if (empty($itemIds)) {
            return redirect()
                ->route('pelanggan.keranjang')
                ->with('error', 'Silakan pilih produk terlebih dahulu.');
        }

        $items = KeranjangItem::with('parfum')
            ->where('user_id', $request->user()->id)
            ->whereIn('id', $itemIds)
            ->get();

        if ($items->isEmpty()) {
            session()->forget('checkout_item_ids');

            return redirect()
                ->route('pelanggan.keranjang')
                ->with('error', 'Produk checkout tidak ditemukan.');
        }
    }

    $alamat = $request->user()->alamat;
    $alamatLengkap = null;

    if ($alamat) {

        $provincesResponse = Http::get(
            'https://wilayah.id/api/provinces.json'
        );

        $provinces = $provincesResponse->successful()
            ? $provincesResponse->json('data', [])
            : [];

        $provinsi = collect($provinces)
            ->firstWhere('code', $alamat->provinsi);

        $regenciesResponse = Http::get(
            "https://wilayah.id/api/regencies/{$alamat->provinsi}.json"
        );

        $regencies = $regenciesResponse->successful()
            ? $regenciesResponse->json('data', [])
            : [];

        $kabupaten = collect($regencies)
            ->firstWhere('code', $alamat->kabupaten_kota);

        $districtsResponse = Http::get(
            "https://wilayah.id/api/districts/{$alamat->kabupaten_kota}.json"
        );

        $districts = $districtsResponse->successful()
            ? $districtsResponse->json('data', [])
            : [];

        $kecamatan = collect($districts)
            ->firstWhere('code', $alamat->kecamatan);

        $villagesResponse = Http::get(
            "https://wilayah.id/api/villages/{$alamat->kecamatan}.json"
        );

        $villages = $villagesResponse->successful()
            ? $villagesResponse->json('data', [])
            : [];

        $desa = collect($villages)
            ->firstWhere('code', $alamat->desa);

        $alamatLengkap = [
            'nama_penerima' => $alamat->nama_penerima,
            'no_hp' => $alamat->no_hp,
            'alamat_lengkap' => $alamat->alamat_lengkap,
            'desa' => $desa['name'] ?? $alamat->desa,
            'kecamatan' => $kecamatan['name'] ?? $alamat->kecamatan,
            'kabupaten_kota' => $kabupaten['name'] ?? $alamat->kabupaten_kota,
            'provinsi' => $provinsi['name'] ?? $alamat->provinsi,
            'kode_pos' => $alamat->kode_pos,
        ];
    }

    return Inertia::render('Pelanggan/Checkout', [
        'items' => $items,
        'alamat' => $alamatLengkap,
        'authUser' => $request->user(),
    ]);
}
   public function checkout(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'item_ids' => ['nullable', 'array'],
        'item_ids.*' => ['integer'],
        'metode_pembayaran' => ['required', 'in:cod,transfer'],
    ]);

    $user = $request->user();
    $alamat = $user->alamat;
    $directCheckout = session('direct_checkout');

    if (!$alamat) {
        return back()->with(
            'error',
            'Silakan isi alamat pengiriman terlebih dahulu.'
        );
    }

    try {

        $pesanan = DB::transaction(function () use (
            $user,
            $alamat,
            $validated,
            $directCheckout
        ) {

            if ($directCheckout) {

    $parfum = Parfum::lockForUpdate()
        ->find($directCheckout['parfum_id']);

    if (!$parfum) {
        throw new \Exception('Produk tidak ditemukan.');
    }

    $item = new KeranjangItem([
        'id' => null,
        'user_id' => $user->id,
        'parfum_id' => $parfum->id,
        'ukuran_ml' => $directCheckout['ukuran_ml'],
        'jumlah' => $directCheckout['jumlah'],
    ]);

    $item->setRelation('parfum', $parfum);

    $items = collect([$item]);

    } else {

        $items = KeranjangItem::with('parfum')
            ->where('user_id', $user->id)
            ->whereIn('id', $validated['item_ids'] ?? [])
            ->lockForUpdate()
            ->get();
    }

            if ($items->isEmpty()) {
                throw new \Exception(
                    'Produk keranjang tidak ditemukan.'
                );
            }

            $totalProduk = 0;

            foreach ($items as $item) {

                if (!$item->parfum) {
                    throw new \Exception(
                        'Produk tidak ditemukan.'
                    );
                }

                if ($item->jumlah > $item->parfum->stok) {
                    throw new \Exception(
                        "Stok {$item->parfum->nama} tidak mencukupi."
                    );
                }

                $hargaPerMl = (float) $item->parfum->harga_per_ml;
                $ukuranMl = (int) $item->ukuran_ml;

                if ($hargaPerMl <= 0) {
                    throw new \Exception(
                        "Harga per ml {$item->parfum->nama} belum diatur."
                    );
                }

                if (!in_array($ukuranMl, [
                        1,
                        5,
                        10,
                        15,
                        20,
                        25,
                        50,
                    ], true)) {
                        throw new \Exception(
                            "Ukuran parfum {$item->parfum->nama} tidak valid."
                        );
                    }

                $hargaUkuran = $hargaPerMl * $ukuranMl;

                $subtotal = $hargaUkuran * $item->jumlah;

                $totalProduk += $subtotal;
            }

            $ongkir = 25000;

            $totalHarga = $totalProduk + $ongkir;

            $pesanan = Pesanan::create([
                'user_id' => $user->id,
                'alamat_id' => $alamat->id,
                'total_harga' => $totalHarga,
                'status' => 'menunggu',
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'status_pembayaran' => 'belum_bayar',
            ]);

            if ($validated['metode_pembayaran'] === 'cod') {
            AdminNotification::create([
                'pesanan_id' => $pesanan->id,
                'is_read' => false,
            ]);
        }

            foreach ($items as $item) {

                $hargaPerMl = (float) $item->parfum->harga_per_ml;
                $ukuranMl = (int) $item->ukuran_ml;

                $hargaUkuran = $hargaPerMl * $ukuranMl;

                $subtotal = $hargaUkuran * $item->jumlah;

                PesananItem::create([
                    'pesanan_id' => $pesanan->id,
                    'parfum_id' => $item->parfum_id,
                    'ukuran_ml' => $ukuranMl,
                    'jumlah' => $item->jumlah,
                    'harga' => $hargaUkuran,
                    'subtotal' => $subtotal,
                ]);

                $item->parfum->decrement(
                    'stok',
                    $item->jumlah
                );

                if (!$directCheckout) {
                    $item->delete();
                }
            }

            return $pesanan;
        });

   } catch (\Exception $e) {
    return back()->with(
        'error',
        $e->getMessage()
    );
}

    if ($validated['metode_pembayaran'] === 'transfer') {
    Config::$serverKey = config('services.midtrans.server_key');
    Config::$isProduction = config('services.midtrans.is_production');
    Config::$isSanitized = true;
    Config::$is3ds = true;

    $snapToken = Snap::getSnapToken([
        'transaction_details' => [
            'order_id' => 'ORDER-' . $pesanan->id,
            'gross_amount' => (int) $pesanan->total_harga,
        ],
        'customer_details' => [
            'first_name' => $user->name,
            'email' => $user->email,
        ],
    ]);

    return redirect()
        ->route('pelanggan.checkout.success', $pesanan->id)
        ->with('snap_token', $snapToken);
    }

    return redirect()->route(
        'pelanggan.checkout.success',
        $pesanan->id
    );
    }
        public function success(Request $request, Pesanan $pesanan)
    {
        if ($pesanan->user_id !== $request->user()->id) {
            abort(403);
        }

        return Inertia::render('Pelanggan/CheckoutSuccess', [
            'pesanan' => $pesanan,
            'snap_token' => session('snap_token'),
        ]);
    }
    public function pesanSekarang(
        Request $request,
        Parfum $parfum
    ): RedirectResponse {
        if ($parfum->stok <= 0) {
            return back()->with(
                'error',
                'Stok parfum sedang habis.'
            );
        }

        $validated = $request->validate([
            'ukuran_ml' => [
                'required',
                'integer',
                'in:1,5,10,15,20,25,50',
            ],

            'jumlah' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        if ($validated['jumlah'] > $parfum->stok) {
            return back()->with(
                'error',
                'Jumlah botol melebihi stok yang tersedia.'
            );
        }

        session([
            'direct_checkout' => [
                'parfum_id' => $parfum->id,
                'ukuran_ml' => $validated['ukuran_ml'],
                'jumlah' => $validated['jumlah'],
            ],
        ]);

        session()->forget('checkout_item_ids');

        return redirect()->route('pelanggan.checkout');
    }
    }