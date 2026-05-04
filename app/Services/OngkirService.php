<?php

namespace App\Services;

use App\Models\Ongkir;

class OngkirService
{
    public const PESAN_ONGKIR_TIDAK_DITEMUKAN = 'Ongkir akan dikonfirmasi admin';

    /**
     * Hitung ongkir aktif dengan prioritas kota, lalu provinsi.
     */
    public function hitung(?string $city, ?string $province): array
    {
        $city = Ongkir::normalizeNama($city);
        $province = Ongkir::normalizeNama($province);

        $ongkir = $city !== ''
            ? $this->findAktif('kota', $city)
            : null;

        if (! $ongkir && $province !== '') {
            $ongkir = $this->findAktif('provinsi', $province);
        }

        if (! $ongkir) {
            return [
                'ditemukan' => false,
                'tipe' => null,
                'nama' => null,
                'harga' => null,
                'pesan' => self::PESAN_ONGKIR_TIDAK_DITEMUKAN,
            ];
        }

        return [
            'ditemukan' => true,
            'tipe' => $ongkir->tipe,
            'nama' => $ongkir->nama,
            'harga' => (float) $ongkir->harga,
            'pesan' => null,
        ];
    }

    private function findAktif(string $tipe, string $nama): ?Ongkir
    {
        return Ongkir::query()
            ->where('tipe', $tipe)
            ->where('nama', $nama)
            ->where('is_active', true)
            ->first();
    }
}
