<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ongkir;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OngkirController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $ongkir = Ongkir::query()
            ->when($request->filled('tipe'), fn ($query) => $query->where('tipe', $request->input('tipe')))
            ->orderBy('tipe')
            ->orderBy('nama')
            ->paginate((int) $request->integer('per_page', 20));

        return response()->json([
            'message' => 'Daftar ongkir berhasil diambil.',
            'data' => $ongkir,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->normalizePayload($request);

        $validated = $request->validate($this->rules(), $this->messages());

        $ongkir = Ongkir::create($validated);

        return response()->json([
            'message' => 'Ongkir berhasil ditambahkan.',
            'data' => $ongkir,
        ], 201);
    }

    public function update(Request $request, Ongkir $ongkir): JsonResponse
    {
        $this->normalizePayload($request);

        $validated = $request->validate($this->rules($ongkir), $this->messages());

        $ongkir->update($validated);

        return response()->json([
            'message' => 'Ongkir berhasil diperbarui.',
            'data' => $ongkir,
        ]);
    }

    public function toggle(Ongkir $ongkir): JsonResponse
    {
        $ongkir->update([
            'is_active' => ! $ongkir->is_active,
        ]);

        return response()->json([
            'message' => $ongkir->is_active
                ? 'Ongkir berhasil diaktifkan.'
                : 'Ongkir berhasil dinonaktifkan.',
            'data' => $ongkir,
        ]);
    }

    private function rules(?Ongkir $ongkir = null): array
    {
        return [
            'tipe' => ['required', Rule::in(['kota', 'provinsi'])],
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('ongkir', 'nama')
                    ->where(fn ($query) => $query->where('tipe', request('tipe')))
                    ->ignore($ongkir),
            ],
            'harga' => ['required', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    private function normalizePayload(Request $request): void
    {
        $request->merge([
            'tipe' => Ongkir::normalizeNama($request->input('tipe')),
            'nama' => Ongkir::normalizeNama($request->input('nama')),
        ]);
    }

    private function messages(): array
    {
        return [
            'tipe.required' => 'Tipe ongkir wajib diisi.',
            'tipe.in' => 'Tipe ongkir harus kota atau provinsi.',
            'nama.required' => 'Nama kota atau provinsi wajib diisi.',
            'nama.unique' => 'Ongkir untuk wilayah ini sudah ada.',
            'harga.required' => 'Harga ongkir wajib diisi.',
            'harga.numeric' => 'Harga ongkir harus berupa angka.',
            'harga.min' => 'Harga ongkir tidak boleh kurang dari 0.',
        ];
    }
}
