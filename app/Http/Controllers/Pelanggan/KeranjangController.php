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
    public function store(Request $request, Parfum $parfum): RedirectResponse
    {
        if ($parfum->stok <= 0) {
            return back()->with('error', 'Stok parfum sedang habis.');
        }

        $item = KeranjangItem::where('user_id', $request->user()->id)
            ->where('parfum_id', $parfum->id)
            ->first();

        if ($item) {
            if ($item->jumlah >= $parfum->stok) {
                return back()->with('error', 'Jumlah melebihi stok yang tersedia.');
            }

            $item->increment('jumlah');
        } else {
            KeranjangItem::create([
                'user_id' => $request->user()->id,
                'parfum_id' => $parfum->id,
                'jumlah' => 1,
            ]);
        }

        return back()->with('success', "{$parfum->nama} berhasil dimasukkan ke keranjang.");
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

            $totalHarga = 0;

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

                $totalHarga +=
                    $item->parfum->harga * $item->jumlah;
            }

            $pesanan = Pesanan::create([
                'user_id' => $user->id,
                'alamat_id' => $alamat->id,
                'total_harga' => $totalHarga,
                'status' => 'menunggu',
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'status_pembayaran' => $validated['metode_pembayaran'] === 'cod'
                    ? 'belum_bayar'
                    : 'sudah_bayar',
            ]);

            foreach ($items as $item) {

                $harga = $item->parfum->harga;

                $subtotal =
                    $harga * $item->jumlah;

                PesananItem::create([
                    'pesanan_id' => $pesanan->id,
                    'parfum_id' => $item->parfum_id,
                    'jumlah' => $item->jumlah,
                    'harga' => $harga,
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