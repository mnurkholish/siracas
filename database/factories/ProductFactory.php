<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_produk' => $this->faker->words(2, true),
            'harga' => $this->faker->numberBetween(5000, 500000),
            'stok' => $this->faker->numberBetween(0, 100),
            'satuan' => $this->faker->randomElement(array_keys(Product::SATUAN)),
            'deskripsi' => $this->faker->sentence(),
            'foto' => null,
        ];
    }
}
