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
            'in:5,10,15,20,25,35,40,45,50',
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
   public function checkout(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'item_ids' => ['required', 'array', 'min:1'],
        'item_ids.*' => ['integer'],
        'metode_pembayaran' => ['required', 'in:cod,transfer'],
    ]);

    $user = $request->user();
    $alamat = $user->alamat;

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
            $validated
        ) {

            $items = KeranjangItem::with('parfum')
                ->where('user_id', $user->id)
                ->whereIn('id', $validated['item_ids'])
                ->lockForUpdate()
                ->get();

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
                'status_pembayaran' =>
                    $validated['metode_pembayaran'] === 'cod'
                        ? 'belum_bayar'
                        : 'sudah_bayar',
            ]);

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

                $item->delete();
            }

            return $pesanan;
        });

    } catch (\Exception $e) {

        return back()->with(
            'error',
            $e->getMessage()
        );
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
    ]);
}
}