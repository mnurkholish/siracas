<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class WilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $baseUrl = 'https://wilayah.id/api';

        $provinces = Http::timeout(60)
            ->get("$baseUrl/provinces.json")
            ->throw()
            ->json('data');

        foreach ($provinces as $prov) {

            DB::table('provinsis')->updateOrInsert(
                ['code' => $prov['code']],
                [
                    'nama' => $prov['name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $provinsi = DB::table('provinsis')
                ->where('code', $prov['code'])
                ->first();

            echo "Provinsi: {$prov['name']}\n";

            $regencies = Http::timeout(60)
                ->get("$baseUrl/regencies/{$prov['code']}.json")
                ->throw()
                ->json('data');

            foreach ($regencies as $reg) {

                DB::table('kotas')->updateOrInsert(
                    ['code' => $reg['code']],
                    [
                        'nama' => $reg['name'],
                        'provinsi_id' => $provinsi->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                $kota = DB::table('kotas')
                    ->where('code', $reg['code'])
                    ->first();

                echo "-- Kota: {$reg['name']}\n";

                $districts = Http::timeout(60)
                    ->get("$baseUrl/districts/{$reg['code']}.json")
                    ->throw()
                    ->json('data');

                foreach ($districts as $dist) {

                    DB::table('kecamatans')->updateOrInsert(
                        ['code' => $dist['code']],
                        [
                            'nama' => $dist['name'],
                            'kota_id' => $kota->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
        }
    }
}
