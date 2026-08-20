<?php

namespace App\Http\Controllers\Pelanggan;
use App\Http\Controllers\Controller;
use App\Models\Alamat;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AlamatController extends Controller
{
    public function create(Request $request): Response
{
    $response = Http::get('https://wilayah.id/api/provinces.json');

    $provinces = $response->successful()
        ? $response->json('data', [])
        : [];

    return Inertia::render('Pelanggan/Alamat', [
        'alamat' => $request->user()->alamat,
        'authUser' => $request->user(),
        'provinces' => $provinces,
    ]);
}
    public function regencies(string $provinceCode)
{
    $response = Http::get(
        "https://wilayah.id/api/regencies/{$provinceCode}.json"
    );

    return response()->json(
        $response->successful()
            ? $response->json('data', [])
            : []
    );
}

    public function districts(string $regencyCode)
{
    $response = Http::get(
        "https://wilayah.id/api/districts/{$regencyCode}.json"
    );

    return response()->json(
        $response->successful()
            ? $response->json('data', [])
            : []
    );
}

    public function villages(string $districtCode)
{
    $response = Http::get(
        "https://wilayah.id/api/villages/{$districtCode}.json"
    );

    return response()->json(
        $response->successful()
            ? $response->json('data', [])
            : []
    );
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