<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        PaymentMethod::updateOrCreate(
            ['kode' => 'cod'],
            [
                'nama' => 'COD',
                'deskripsi' => 'Bayar saat pesanan diterima.',
                'aktif' => true,
            ]
        );

        PaymentMethod::updateOrCreate(
            ['kode' => 'transfer'],
            [
                'nama' => 'Transfer Bank',
                'deskripsi' => 'Bayar melalui transfer bank.',
                'aktif' => true,
            ]
        );
    }
}