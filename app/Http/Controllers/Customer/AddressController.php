<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Kecamatan;
use App\Models\Kota;
use App\Models\Provinsi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AddressController extends Controller
{
    public function create(Request $request)
    {
        $provinsis = Provinsi::query()
            ->orderBy('nama')
            ->get();

        return view('customer.addresses.create', [
            'provinsis' => $provinsis,
            'returnTo' => $this->safeReturnTo($request->query('return_to')),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'provinsi_id' => ['required', 'integer', 'exists:provinsis,id'],
            'kota_id' => [
                'required',
                'integer',
                Rule::exists('kotas', 'id')->where('provinsi_id', $request->input('provinsi_id')),
            ],
            'kecamatan_id' => [
                'required',
                'integer',
                Rule::exists('kecamatans', 'id')->where('kota_id', $request->input('kota_id')),
            ],
            'detail_alamat' => ['required', 'string', 'max:1000'],
            'return_to' => ['nullable', 'string'],
        ], [
            'provinsi_id.required' => 'Provinsi wajib dipilih.',
            'provinsi_id.exists' => 'Provinsi tidak valid.',
            'kota_id.required' => 'Kota wajib dipilih.',
            'kota_id.exists' => 'Kota tidak valid.',
            'kecamatan_id.required' => 'Kecamatan wajib dipilih.',
            'kecamatan_id.exists' => 'Kecamatan tidak valid.',
            'detail_alamat.required' => 'Detail alamat wajib diisi.',
            'detail_alamat.max' => 'Detail alamat maksimal 1000 karakter.',
        ]);

        Address::create([
            'user_id' => Auth::id(),
            'kecamatan_id' => $validated['kecamatan_id'],
            'detail_alamat' => $validated['detail_alamat'],
        ]);

        return redirect($this->safeReturnTo($validated['return_to'] ?? null))
            ->with('success', 'Alamat berhasil dibuat!');
    }

    public function kotas(Request $request)
    {
        $validated = $request->validate([
            'provinsi_id' => ['required', 'integer', 'exists:provinsis,id'],
        ]);

        return Kota::query()
            ->where('provinsi_id', $validated['provinsi_id'])
            ->orderBy('nama')
            ->get(['id', 'nama']);
    }

    public function kecamatans(Request $request)
    {
        $validated = $request->validate([
            'kota_id' => ['required', 'integer', 'exists:kotas,id'],
        ]);

        return Kecamatan::query()
            ->where('kota_id', $validated['kota_id'])
            ->orderBy('nama')
            ->get(['id', 'nama']);
    }

    private function safeReturnTo(?string $returnTo): string
    {
        if (!$returnTo || !str_starts_with($returnTo, '/')) {
            return route('profile', absolute: false);
        }

        if (str_starts_with($returnTo, '//')) {
            return route('profile', absolute: false);
        }

        return $returnTo;
    }
}
