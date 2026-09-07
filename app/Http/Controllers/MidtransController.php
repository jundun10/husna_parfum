<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Midtrans\Config;
use Midtrans\Notification;

class MidtransController extends Controller
{
    public function notification(Request $request): JsonResponse
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        try {
            $notification = new Notification();

            $orderId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status;

            // ORDER-123 -> 123
            $pesananId = str_replace('ORDER-', '', $orderId);

            $pesanan = Pesanan::find($pesananId);

            if (!$pesanan) {
                return response()->json([
                    'message' => 'Pesanan tidak ditemukan.',
                ], 404);
            }

            if (
                $transactionStatus === 'capture' &&
                $fraudStatus === 'accept'
            ) {
                $pesanan->update([
                    'status_pembayaran' => 'sudah_bayar',
                ]);
            } elseif ($transactionStatus === 'settlement') {
                $pesanan->update([
                    'status_pembayaran' => 'sudah_bayar',
                ]);
            } elseif (
                in_array($transactionStatus, [
                    'deny',
                    'cancel',
                    'expire',
                ], true)
            ) {
                $pesanan->update([
                    'status_pembayaran' => 'gagal',
                ]);
            }

            return response()->json([
                'message' => 'Notification berhasil diproses.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Gagal memproses notification.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}