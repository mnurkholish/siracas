<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Kecamatan;
use App\Models\Kota;
use App\Models\Product;
use App\Models\Provinsi;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $kecamatan = $this->demoKecamatan();
        $products = Product::query()->oldest('id')->get();
        $customers = User::query()
            ->whereIn('email', UserSeeder::DEMO_CUSTOMER_EMAILS)
            ->get()
            ->sortBy(fn (User $user) => array_search($user->email, UserSeeder::DEMO_CUSTOMER_EMAILS, true))
            ->values();

        if ($products->isEmpty() || $customers->isEmpty()) {
            return;
        }

        foreach ($customers as $index => $customer) {
            $address = Address::query()->firstOrCreate(
                [
                    'user_id' => $customer->id,
                    'detail_alamat' => 'Jl. Demo SIRACAS No. '.($index + 1),
                ],
                [
                    'kecamatan_id' => $kecamatan->id,
                ]
            );

            $completedAt = now()->subDays($index + 2);
            $transaction = Transaction::query()->updateOrCreate(
                [
                    'order_id' => 'SIRACAS-SEED-'.$customer->id.'-SELESAI',
                ],
                [
                    'user_id' => $customer->id,
                    'address_id' => $address->id,
                    'tanggal' => $completedAt->copy()->subDay(),
                    'catatan' => 'Transaksi demo untuk data penilaian.',
                    'status' => 'selesai',
                    'ongkir' => 12000,
                    'snap_token' => null,
                    'payment_type' => 'bank_transfer',
                    'paid_at' => $completedAt->copy()->subDay(),
                    'completed_at' => $completedAt,
                ]
            );

            $transaction->transactionDetails()->delete();

            $firstProduct = $products[$index % $products->count()];
            $secondProduct = $products[($index + 1) % $products->count()];

            $transaction->transactionDetails()->create([
                'product_id' => $firstProduct->id,
                'quantity' => 1 + ($index % 2),
                'harga_saat_transaksi' => $firstProduct->harga,
            ]);

            if ($products->count() > 1 && $index < 3) {
                $transaction->transactionDetails()->create([
                    'product_id' => $secondProduct->id,
                    'quantity' => 1,
                    'harga_saat_transaksi' => $secondProduct->harga,
                ]);
            }
        }

        $customer = $customers->firstWhere('email', 'customer@gmail.com') ?? $customers->first();
        $address = $customer->addresses()->first();
        $product = $products->first();

        $activeTransaction = Transaction::query()->updateOrCreate(
            [
                'order_id' => 'SIRACAS-SEED-'.$customer->id.'-DIPROSES',
            ],
            [
                'user_id' => $customer->id,
                'address_id' => $address->id,
                'tanggal' => now()->subDay(),
                'catatan' => 'Transaksi demo yang belum bisa dinilai.',
                'status' => 'diproses',
                'ongkir' => 12000,
                'snap_token' => null,
                'payment_type' => 'bank_transfer',
                'paid_at' => now()->subDay(),
                'completed_at' => null,
            ]
        );

        $activeTransaction->transactionDetails()->delete();
        $activeTransaction->transactionDetails()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'harga_saat_transaksi' => $product->harga,
        ]);
    }

    private function demoKecamatan(): Kecamatan
    {
        $provinsi = Provinsi::query()->firstOrCreate(
            ['code' => '35'],
            ['nama' => 'Jawa Timur']
        );

        $kota = Kota::query()->firstOrCreate(
            ['code' => '35.78'],
            [
                'provinsi_id' => $provinsi->id,
                'nama' => 'Kota Surabaya',
            ]
        );

        return Kecamatan::query()->firstOrCreate(
            ['code' => '35.78.01'],
            [
                'kota_id' => $kota->id,
                'nama' => 'Genteng',
            ]
        );
    }
}
