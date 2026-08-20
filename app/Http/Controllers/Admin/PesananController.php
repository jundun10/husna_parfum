<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Inertia\Response;

class PesananController extends Controller
{
    public function index(): Response
    {
        $pesanans = Pesanan::with([
            'user:id,name,email',
            'alamat:id,user_id,nama_penerima,no_hp,provinsi,kabupaten_kota,kecamatan,desa,alamat_lengkap,kode_pos',
            'items.parfum:id,nama,harga,foto',
        ])
        ->latest()
        ->get();

        $pesanans->each(function ($pesanan) {

            if (!$pesanan->alamat) {
                return;
            }

            $alamat = $pesanan->alamat;

            $provinceCode = $alamat->provinsi;

            $regencyCode = $this->getParentCode(
                $alamat->kabupaten_kota,
                2
            );

            $districtCode = $this->getParentCode(
                $alamat->kecamatan,
                3
            );

            $villageCode = $alamat->desa;

            $province = $this->findRegion(
                'provinces',
                $provinceCode
            );


            $regency = $this->findRegion(
                "regencies/{$provinceCode}",
                $regencyCode
            );


            $district = $this->findRegion(
                "districts/{$regencyCode}",
                $districtCode
            );

            $village = $this->findRegion(
                "villages/{$districtCode}",
                $villageCode
            );

            $alamat->provinsi_nama =
                $province['name'] ?? $provinceCode;

            $alamat->kabupaten_nama =
                $regency['name'] ?? $regencyCode;

            $alamat->kecamatan_nama =
                $district['name'] ?? $districtCode;

            $alamat->desa_nama =
                $village['name'] ?? $villageCode;

            $alamat->maps_url =
                'https://www.google.com/maps/search/?api=1&query='
                . urlencode(
                    implode(', ', array_filter([
                        $alamat->alamat_lengkap,
                        $alamat->desa_nama,
                        $alamat->kecamatan_nama,
                        $alamat->kabupaten_nama,
                        $alamat->provinsi_nama,
                        $alamat->kode_pos,
                    ]))
                );
        });

        return Inertia::render('Admin/Pesanan', [
            'pesanans' => $pesanans,
            'authUser' => request()->user(),
        ]);
    }

    private function getParentCode(
        ?string $code,
        int $segments
    ): ?string {
        if (!$code) {
            return null;
        }

        $parts = explode('.', $code);

        return implode(
            '.',
            array_slice($parts, 0, $segments)
        );
    }

    private function findRegion(
        string $endpoint,
        ?string $code
    ): ?array {
        if (!$code) {
            return null;
        }

        $response = Http::get(
            "https://wilayah.id/api/{$endpoint}.json"
        );

        if (!$response->successful()) {
            return null;
        }

        $data = $response->json('data', []);

        foreach ($data as $region) {

            if (($region['code'] ?? null) === $code) {
                return $region;
            }

        }

        return null;
    }


    public function updateStatus(
        Request $request,
        Pesanan $pesanan
    ) {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:menunggu,diproses,dikirim,diterima,selesai,dibatalkan',
            ],
        ]);

        $pesanan->update([
            'status' => $validated['status'],
        ]);

        return back()->with(
            'success',
            'Status pesanan berhasil diperbarui.'
        );
    }
}