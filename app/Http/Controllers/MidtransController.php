<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use App\Models\Pesanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MidtransController extends Controller
{
    public function notification(Request $request): JsonResponse
    {
        try {
            $orderId = $request->input('order_id');
            $statusCode = $request->input('status_code');
            $grossAmount = $request->input('gross_amount');
            $signatureKey = $request->input('signature_key');

            // Pastikan data utama tersedia
            if (
                !$orderId ||
                !$statusCode ||
                !$grossAmount ||
                !$signatureKey
            ) {
                return response()->json([
                    'message' => 'Data notification tidak lengkap.',
                ], 400);
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDASI SIGNATURE MIDTRANS
            |--------------------------------------------------------------------------
            */

            $serverKey = config('services.midtrans.server_key');

            $expectedSignature = hash(
                'sha512',
                $orderId . $statusCode . $grossAmount . $serverKey
            );

            if (!hash_equals($expectedSignature, $signatureKey)) {
                Log::warning('Signature Midtrans tidak valid.', [
                    'order_id' => $orderId,
                ]);

                return response()->json([
                    'message' => 'Signature tidak valid.',
                ], 403);
            }

            /*
            |--------------------------------------------------------------------------
            | CARI PESANAN
            |--------------------------------------------------------------------------
            */

            $pesananId = Str::replaceFirst(
                'ORDER-',
                '',
                $orderId
            );

            $pesanan = Pesanan::find($pesananId);

            if (!$pesanan) {
                return response()->json([
                    'message' => 'Pesanan tidak ditemukan.',
                ], 404);
            }

            $transactionStatus = $request->input('transaction_status');
            $fraudStatus = $request->input('fraud_status');

            /*
            |--------------------------------------------------------------------------
            | PEMBAYARAN BERHASIL
            |--------------------------------------------------------------------------
            */

            $pembayaranBerhasil = false;

            if (
                $transactionStatus === 'capture' &&
                $fraudStatus === 'accept'
            ) {
                $pembayaranBerhasil = true;
            }

            if ($transactionStatus === 'settlement') {
                $pembayaranBerhasil = true;
            }

            if ($pembayaranBerhasil) {
                $pesanan->update([
                    'status_pembayaran' => 'sudah_bayar',
                ]);

                // Transfer baru membuat notifikasi setelah berhasil bayar
                if ($pesanan->metode_pembayaran === 'transfer') {
                    AdminNotification::firstOrCreate(
                        [
                            'pesanan_id' => $pesanan->id,
                        ],
                        [
                            'is_read' => false,
                        ]
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | PEMBAYARAN GAGAL / EXPIRED / DIBATALKAN
            |--------------------------------------------------------------------------
            */

            elseif (in_array($transactionStatus, [
                'deny',
                'cancel',
                'expire',
            ], true)) {
                $pesanan->update([
                    'status_pembayaran' => 'gagal',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | PEMBAYARAN MASIH MENUNGGU
            |--------------------------------------------------------------------------
            */

            elseif ($transactionStatus === 'pending') {
                $pesanan->update([
                    'status_pembayaran' => 'belum_bayar',
                ]);
            }

            return response()->json([
                'message' => 'Notification berhasil diproses.',
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Midtrans notification error', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Gagal memproses notification.',
            ], 500);
        }
    }
}