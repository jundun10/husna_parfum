<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\KeranjangItem;
use App\Models\Parfum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
}