<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\TransactionDetail;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $reviews = [
            'Cacing segar saya terima besar-besar dan bagus kualitasnya. Kemasan juga aman dan tidak berbau berlebihan.',
            'Sudah beberapa kali order di sini dan selalu puas. Pelayanannya cepat, respon admin baik, dan produknya konsisten.',
            'Pupuk kascing sangat membantu tanaman saya jadi lebih subur. Teksturnya halus dan mudah digunakan.',
            'Tepung cacing berkualitas, cocok dipakai untuk usaha pakan saya. Tekstur halus dan stok sering tersedia.',
            'Kascing sangat ringan dan membuat drainase media tanam jauh lebih baik. Proses pemesanan juga mudah dan tidak ribet.',
            'Cacing saya diterima masih dalam kondisi aktif dan segar. Sangat puas dengan kebersihan pengiriman.',
        ];

        $details = TransactionDetail::query()
            ->with('transaction.user')
            ->whereHas('transaction', function ($query) {
                $query->where('status', 'selesai');
            })
            ->oldest('id')
            ->get();

        if ($details->isEmpty()) {
            return;
        }

        $keptUnreviewedForDefaultCustomer = false;

        foreach ($details as $index => $detail) {
            if (
                ! $keptUnreviewedForDefaultCustomer &&
                $detail->transaction?->user?->email === 'customer@gmail.com'
            ) {
                $keptUnreviewedForDefaultCustomer = true;

                continue;
            }

            Review::query()->updateOrCreate(
                [
                    'transaction_detail_id' => $detail->id,
                ],
                [
                    'user_id' => $detail->transaction->user_id,
                    'product_id' => $detail->product_id,
                    'isi' => $reviews[$index % count($reviews)],
                    'rating' => $index % 4 === 0 ? 4 : 5,
                    'foto' => null,
                ]
            );
        }
    }
}
