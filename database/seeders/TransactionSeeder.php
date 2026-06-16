<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Kecamatan;
use App\Models\Kota;
use App\Models\Product;
use App\Models\Provinsi;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class TransactionSeeder extends Seeder
{
    private const ORDER_PREFIX = 'SIRACAS-SEED';

    private const VALID_STATUSES = [
        'dibayar',
        'diproses',
        'dikirim',
        'diterima',
        'selesai',
    ];

    private const WEEKDAY_BASKETS = [
        1 => [
            ['index' => 3, 'quantity' => 2],
            ['index' => 1, 'quantity' => 2],
        ],
        2 => [
            ['index' => 0, 'quantity' => 3],
        ],
        3 => [
            ['index' => 2, 'quantity' => 2],
            ['index' => 1, 'quantity' => 1],
        ],
        4 => [
            ['index' => 3, 'quantity' => 1],
            ['index' => 1, 'quantity' => 2],
        ],
        5 => [
            ['index' => 4, 'quantity' => 2],
            ['index' => 1, 'quantity' => 1],
        ],
        6 => [
            ['index' => 0, 'quantity' => 4],
            ['index' => 2, 'quantity' => 2],
            ['index' => 3, 'quantity' => 1],
        ],
        7 => [
            ['index' => 3, 'quantity' => 3],
            ['index' => 0, 'quantity' => 2],
        ],
    ];

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

        $this->deleteLegacyEwmaDemoTransactions();

        $addresses = $this->addressesFor($customers, $kecamatan);

        foreach ($customers as $index => $customer) {
            $address = $addresses[$customer->id];

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
                    'received_at' => $completedAt->copy()->subHours(3),
                    'warranty_status' => 'tidak_ada',
                    'warranty_claimed_at' => null,
                    'warranty_resolved_at' => null,
                    'refund_amount' => 0,
                    'refund_note' => null,
                    'refunded_at' => null,
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

        $this->seedForecastTransactions($customers, $addresses, $products);

        $customer = $customers->firstWhere('email', 'customer@gmail.com') ?? $customers->first();
        $address = $addresses[$customer->id];
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
                'received_at' => null,
                'warranty_status' => 'tidak_ada',
                'warranty_claimed_at' => null,
                'warranty_resolved_at' => null,
                'refund_amount' => 0,
                'refund_note' => null,
                'refunded_at' => null,
            ]
        );

        $activeTransaction->transactionDetails()->delete();
        $activeTransaction->transactionDetails()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'harga_saat_transaksi' => $product->harga,
        ]);
    }

    private function seedForecastTransactions(Collection $customers, Collection $addresses, Collection $products): void
    {
        $today = now()->startOfDay();
        $start = $today->copy()->startOfMonth();

        foreach (Carbon::parse($start)->daysUntil($today->copy()->addDay()) as $dayIndex => $date) {
            $this->seedDailyForecastTransaction(
                $date->copy(),
                $dayIndex,
                $customers,
                $addresses,
                $products
            );

            if ($date->day % 8 === 0) {
                $this->seedRefundExample(
                    $date->copy(),
                    $dayIndex,
                    $customers,
                    $addresses,
                    $products
                );
            }

            if ($date->day % 10 === 0) {
                $this->seedWarrantyRequestExample(
                    $date->copy(),
                    $dayIndex,
                    $customers,
                    $addresses,
                    $products
                );
            }

            if ($date->day % 6 === 0) {
                $this->seedIgnoredExample(
                    $date->copy(),
                    $dayIndex,
                    $customers,
                    $addresses,
                    $products
                );
            }
        }
    }

    private function deleteLegacyEwmaDemoTransactions(): void
    {
        Transaction::query()
            ->where('order_id', 'like', 'SIRACAS-EWMA-DEMO-%')
            ->delete();
    }

    private function seedDailyForecastTransaction(
        Carbon $date,
        int $dayIndex,
        Collection $customers,
        Collection $addresses,
        Collection $products
    ): void {
        $customer = $customers[$dayIndex % $customers->count()];
        $status = self::VALID_STATUSES[$dayIndex % count(self::VALID_STATUSES)];
        $paidAt = $date->copy()->setTime(9 + ($dayIndex % 7), 15);

        $transaction = Transaction::query()->updateOrCreate(
            [
                'order_id' => $this->orderId($date, 'HARIAN'),
            ],
            [
                'user_id' => $customer->id,
                'address_id' => $addresses[$customer->id]->id,
                'tanggal' => $paidAt,
                'catatan' => 'Transaksi demo laporan dengan pola penjualan berbeda tiap hari.',
                'status' => $status,
                'ongkir' => $this->shippingFor($date),
                'snap_token' => null,
                'payment_type' => 'bank_transfer',
                'paid_at' => $paidAt,
                'completed_at' => in_array($status, ['diterima', 'selesai'], true)
                    ? $paidAt->copy()->addHours(8)
                    : null,
                'received_at' => in_array($status, ['diterima', 'selesai'], true)
                    ? $paidAt->copy()->addHours(6)
                    : null,
                'warranty_status' => 'tidak_ada',
                'warranty_claimed_at' => null,
                'warranty_resolved_at' => null,
                'refund_amount' => 0,
                'refund_note' => null,
                'refunded_at' => null,
            ]
        );

        $transaction->transactionDetails()->delete();

        foreach ($this->basketFor($date, $dayIndex) as $item) {
            $product = $products[$item['index'] % $products->count()];

            $transaction->transactionDetails()->create([
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'harga_saat_transaksi' => $product->harga,
            ]);
        }
    }

    private function seedRefundExample(
        Carbon $date,
        int $dayIndex,
        Collection $customers,
        Collection $addresses,
        Collection $products
    ): void {
        $customer = $customers[($dayIndex + 1) % $customers->count()];
        $product = $products[3 % $products->count()];
        $paidAt = $date->copy()->setTime(15, 30);

        $transaction = Transaction::query()->updateOrCreate(
            [
                'order_id' => $this->orderId($date, 'REFUND'),
            ],
            [
                'user_id' => $customer->id,
                'address_id' => $addresses[$customer->id]->id,
                'tanggal' => $paidAt,
                'catatan' => 'Transaksi demo dengan refund sebagian untuk contoh laporan.',
                'status' => 'selesai',
                'ongkir' => 15000,
                'snap_token' => null,
                'payment_type' => 'bank_transfer',
                'paid_at' => $paidAt,
                'completed_at' => $paidAt->copy()->addHours(5),
                'received_at' => $paidAt->copy()->addHours(4),
                'warranty_status' => 'diterima',
                'warranty_claimed_at' => $paidAt->copy()->addHours(5),
                'warranty_resolved_at' => $paidAt->copy()->addHours(7),
                'refund_amount' => 25000,
                'refund_note' => 'Refund sebagian karena kelebihan pembayaran ongkir.',
                'refunded_at' => $paidAt->copy()->addHours(7),
            ]
        );

        $transaction->transactionDetails()->delete();
        $transaction->transactionDetails()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'harga_saat_transaksi' => $product->harga,
        ]);
    }

    private function seedWarrantyRequestExample(
        Carbon $date,
        int $dayIndex,
        Collection $customers,
        Collection $addresses,
        Collection $products
    ): void {
        $customer = $customers[($dayIndex + 3) % $customers->count()];
        $product = $products[0];
        $receivedAt = $date->copy()->setTime(13, 20);

        $transaction = Transaction::query()->updateOrCreate(
            [
                'order_id' => $this->orderId($date, 'GARANSI'),
            ],
            [
                'user_id' => $customer->id,
                'address_id' => $addresses[$customer->id]->id,
                'tanggal' => $receivedAt->copy()->subDay(),
                'catatan' => 'Transaksi demo dengan pengajuan garansi yang menunggu keputusan admin.',
                'status' => 'diterima',
                'ongkir' => 12000,
                'snap_token' => null,
                'payment_type' => 'bank_transfer',
                'paid_at' => $receivedAt->copy()->subDay(),
                'completed_at' => null,
                'received_at' => $receivedAt,
                'warranty_status' => 'diajukan',
                'warranty_claimed_at' => $receivedAt->copy()->addHours(2),
                'warranty_resolved_at' => null,
                'refund_amount' => 0,
                'refund_note' => null,
                'refunded_at' => null,
            ]
        );

        $transaction->transactionDetails()->delete();
        $transaction->transactionDetails()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'harga_saat_transaksi' => $product->harga,
        ]);
    }

    private function seedIgnoredExample(
        Carbon $date,
        int $dayIndex,
        Collection $customers,
        Collection $addresses,
        Collection $products
    ): void {
        $customer = $customers[($dayIndex + 2) % $customers->count()];
        $product = $products[4 % $products->count()];
        $createdAt = $date->copy()->setTime(18, 45);

        $transaction = Transaction::query()->updateOrCreate(
            [
                'order_id' => $this->orderId($date, 'BELUM-VALID'),
            ],
            [
                'user_id' => $customer->id,
                'address_id' => $addresses[$customer->id]->id,
                'tanggal' => $createdAt,
                'catatan' => 'Transaksi demo yang tidak masuk pendapatan laporan.',
                'status' => $date->day % 12 === 0 ? 'dibatalkan' : 'menunggu_pembayaran',
                'ongkir' => 12000,
                'snap_token' => null,
                'payment_type' => null,
                'paid_at' => null,
                'completed_at' => null,
                'received_at' => null,
                'warranty_status' => 'tidak_ada',
                'warranty_claimed_at' => null,
                'warranty_resolved_at' => null,
                'refund_amount' => 0,
                'refund_note' => null,
                'refunded_at' => null,
            ]
        );

        $transaction->transactionDetails()->delete();
        $transaction->transactionDetails()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'harga_saat_transaksi' => $product->harga,
        ]);
    }

    private function basketFor(Carbon $date, int $dayIndex): array
    {
        $growthQuantity = intdiv($dayIndex, 14);

        return collect(self::WEEKDAY_BASKETS[$date->dayOfWeekIso])
            ->map(function (array $item) use ($growthQuantity) {
                $item['quantity'] += $growthQuantity;

                return $item;
            })
            ->all();
    }

    private function shippingFor(Carbon $date): int
    {
        return $date->isWeekend() ? 18000 : 12000;
    }

    private function addressesFor(Collection $customers, Kecamatan $kecamatan): Collection
    {
        return $customers->mapWithKeys(fn (User $customer, int $index) => [
            $customer->id => Address::query()->firstOrCreate(
                [
                    'user_id' => $customer->id,
                    'detail_alamat' => 'Jl. Demo SIRACAS No. '.($index + 1),
                ],
                [
                    'kecamatan_id' => $kecamatan->id,
                ]
            ),
        ]);
    }

    private function orderId(Carbon $date, string $type): string
    {
        return sprintf('%s-%s-%s', self::ORDER_PREFIX, $date->format('Ymd'), $type);
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
