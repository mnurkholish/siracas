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
        Product::create([
                'nama_produk' => 'Cacing Merah',
                'harga' => 15000,
                'stok' => 50,
                'satuan' => 'kg',
                'deskripsi' => 'Cacing kualitas premium',
                'foto' => null,
            ]);

        Product::create([
            'nama_produk' => 'Pupuk Kascing',
            'harga' => 14000,
            'stok' => 40,
            'satuan' => 'kg',
            'deskripsi' => 'Pupuk berkualitas',
            'foto' => null,
        ]);

        // Generate otomatis
        Product::factory()->count(15)->create();

    }
}
