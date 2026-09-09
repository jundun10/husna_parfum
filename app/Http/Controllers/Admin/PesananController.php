<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\AdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Cache;

class PesananController extends Controller
{
    public function index(): Response
    {
        $pesanans = Pesanan::with([
            'user:id,name,email',
            'alamat:id,user_id,nama_penerima,no_hp,provinsi,kabupaten_kota,kecamatan,desa,alamat_lengkap,kode_pos',
            'items.parfum:id,nama,harga,foto',
       ])
        ->where(function ($query) {
            $query
                ->where('metode_pembayaran', 'cod')
                ->orWhere('status_pembayaran', 'sudah_bayar');
        })
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

        $notifications = AdminNotification::with([
            'pesanan.user:id,name',
            'pesanan.items.parfum:id,nama',
        ])
            ->where('is_read', false)
            ->latest()
            ->get();

        $notificationCount = $notifications->count();

        return Inertia::render(
        request()->is('super-admin/*')
            ? 'SuperAdmin/Pesanan'
            : 'Admin/Pesanan',
        [
            'pesanans' => $pesanans,
            'authUser' => request()->user(),
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
        ]
        );
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

    $data = Cache::remember(
        'wilayah_' . $endpoint,
        now()->addHours(24),
        function () use ($endpoint) {
            try {
                $response = Http::timeout(5)->get(
                    "https://wilayah.id/api/{$endpoint}.json"
                );

                if (!$response->successful()) {
                    return [];
                }

                return $response->json('data', []);
            } catch (\Throwable $e) {
                return [];
            }
        }
    );

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
    public function markNotificationAsRead(
    AdminNotification $notification
)   {
    $notification->update([
        'is_read' => true,
    ]);

    if (request()->is('super-admin/*')) {
        return redirect()->route('superadmin.pesanan');
    }

    return redirect()->route('admin.pesanan');
    }
}