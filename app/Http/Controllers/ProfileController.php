<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        return view('customer.profile.index');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // ✅ 1. VALIDASI
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['nullable', 'in:laki-laki,perempuan'],
            'foto_profil' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'hapus_foto' => ['nullable', 'boolean'],
        ]);

        // ✅ 2. HANDLE HAPUS FOTO

        // ✅ 2. HANDLE HAPUS FOTO (PRIORITAS UTAMA)
        if ($request->boolean('hapus_foto')) {
            $this->deleteOldPhoto($user->foto_profil);
            $user->foto_profil = null;
        }

        // ✅ 3. HANDLE UPLOAD (HANYA JIKA TIDAK HAPUS)
        elseif ($request->hasFile('foto_profil')) {
            $file = $request->file('foto_profil');

            if ($file->isValid()) {

                $this->deleteOldPhoto($user->foto_profil);

                $filename = time() . '_' . $file->getClientOriginalName();

                try {
                    $path = $file->storeAs('profile_photos', $filename, 'public');

                    if (!$path) {
                        throw new \Exception('Gagal menyimpan file');
                    }

                    $user->foto_profil = $path;

                } catch (\Throwable $e) {

                    $destination = storage_path('app/public/profile_photos');

                    if (!is_dir($destination)) {
                        mkdir($destination, 0777, true);
                    }

                    $file->move($destination, $filename);

                    $user->foto_profil = 'profile_photos/' . $filename;
                }
            }
        }


        // ✅ 4. UPDATE DATA USER
        $user->fill([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
        ]);

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    // 🔥 HELPER METHOD (biar reusable & clean)
    private function deleteOldPhoto($path)
    {
        try {
            if (!empty($path) && is_string($path)) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal hapus foto lama: ' . $e->getMessage());
        }
    }
}
