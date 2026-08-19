<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Alamat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AlamatController extends Controller
{
    public function create(Request $request): Response
    {
        return Inertia::render('Pelanggan/Alamat', [
            'alamat' => $request->user()->alamat,
            'authUser' => $request->user(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_penerima' => ['required', 'string', 'max:255'],
            'no_hp' => ['required', 'string', 'max:30'],
            'provinsi' => ['required', 'string', 'max:100'],
            'kabupaten_kota' => ['required', 'string', 'max:100'],
            'kecamatan' => ['required', 'string', 'max:100'],
            'desa' => ['required', 'string', 'max:100'],
            'alamat_lengkap' => ['required', 'string'],
            'kode_pos' => ['required', 'string', 'max:10'],
        ]);

        Alamat::updateOrCreate(
            ['user_id' => $request->user()->id],
            $validated
        );

        return redirect()
            ->route('pelanggan.keranjang')
            ->with('success', 'Alamat berhasil disimpan.');
    }
}