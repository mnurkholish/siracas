<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'nama_produk' => 'Cacing Tanah Hidup (Lumbricus)',
                'harga' => 45000,
                'stok' => 150,
                'satuan' => 'kg',
                'deskripsi' => 'Cacing hidup kualitas unggul untuk kebutuhan umpan pancing atau pengolahan limbah organik rumah tangga.',
                'foto' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_produk' => 'Tepung Cacing Murni (Vermipowder)',
                'harga' => 125000,
                'stok' => 40,
                'satuan' => '500g',
                'deskripsi' => 'Konsentrat protein tinggi hasil ekstraksi cacing kering, ideal sebagai campuran pakan ternak atau suplemen kesehatan hewan.',
                'foto' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_produk' => 'Cacing Beku (Frozen Worm)',
                'harga' => 35000,
                'stok' => 85,
                'satuan' => 'pack',
                'deskripsi' => 'Cacing segar yang dibekukan secara cepat untuk menjaga nutrisi, sangat baik sebagai pakan ikan hias dan reptil.',
                'foto' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_produk' => 'Pupuk Organik Kascing',
                'harga' => 70000,
                'stok' => 500,
                'satuan' => 'karung',
                'deskripsi' => 'Produk sampingan berupa bekas cacing (kascing) yang kaya akan unsur hara mikro dan makro untuk kesuburan tanah.',
                'foto' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_produk' => 'Kapsul Ekstrak Cacing',
                'harga' => 75000,
                'stok' => 60,
                'satuan' => 'botol',
                'deskripsi' => 'Ekstrak cacing dalam bentuk kapsul untuk membantu meredakan demam dan mendukung kesehatan pencernaan manusia.',
                'foto' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        };

        // Generate otomatis
        // Product::factory()->count(15)->create();

    }
}
