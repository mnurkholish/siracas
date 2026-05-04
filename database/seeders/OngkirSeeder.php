<?php

namespace Database\Seeders;

use App\Models\Ongkir;
use Illuminate\Database\Seeder;

class OngkirSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'kota' => [
                'jember' => 60000,
                'banyuwangi' => 300000,
                'situbondo' => 325000,
                'probolinggo' => 450000,
                'malang' => 600000,
                'surabaya' => 650000,
                'kediri' => 800000,
                'madiun' => 950000,
                'surakarta' => 1150000,
                'yogyakarta' => 1250000,
                'semarang' => 1300000,
                'tegal' => 1600000,
                'cirebon' => 1800000,
                'bandung' => 2000000,
                'jakarta' => 2300000,
                'bogor' => 2200000,
                'depok' => 2250000,
                'bekasi' => 2200000,
                'tangerang' => 2400000,
                'serang' => 2550000,
            ],
            'provinsi' => [
                'bali' => 950000,
                'nusa tenggara barat' => 1650000,
                'nusa tenggara timur' => 3200000,
                'lampung' => 3000000,
                'sumatera selatan' => 3500000,
                'bengkulu' => 4000000,
                'jambi' => 4100000,
                'kepulauan bangka belitung' => 4200000,
                'riau' => 4900000,
                'kepulauan riau' => 5200000,
                'sumatera barat' => 5100000,
                'sumatera utara' => 6000000,
                'aceh' => 6900000,
                'kalimantan barat' => 3700000,
                'kalimantan tengah' => 3300000,
                'kalimantan selatan' => 3000000,
                'kalimantan timur' => 4300000,
                'kalimantan utara' => 5000000,
                'sulawesi selatan' => 4000000,
                'sulawesi barat' => 4300000,
                'sulawesi tenggara' => 4600000,
                'sulawesi tengah' => 4800000,
                'gorontalo' => 5500000,
                'sulawesi utara' => 6100000,
                'maluku' => 6600000,
                'maluku utara' => 7000000,
                'papua barat' => 8500000,
                'papua barat daya' => 8300000,
                'papua' => 10500000,
                'papua tengah' => 10200000,
                'papua pegunungan' => 12000000,
                'papua selatan' => 11500000,
            ],
        ];

        foreach ($data as $tipe => $items) {
            foreach ($items as $nama => $harga) {
                Ongkir::updateOrCreate(
                    ['tipe' => $tipe, 'nama' => $nama],
                    ['harga' => $harga, 'is_active' => true]
                );
            }
        }
    }
}
