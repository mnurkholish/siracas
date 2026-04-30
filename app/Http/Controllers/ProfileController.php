<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        return view('customer.profile.index');
    }

    public function adminIndex()
    {
        return view('admin.profile.index');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validateWithBag('profileUpdate', [
            'username' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'tanggal_lahir' => ['required', 'date', 'before_or_equal:' . Carbon::now()->subYears(17)->format('Y-m-d'),],
            'jenis_kelamin' => ['required', 'in:laki-laki,perempuan'],
            'foto_profil' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'hapus_foto' => ['nullable', 'boolean'],
            'nomor_hp' => ['required', 'regex:/^08[0-9]{8,11}$/'],
        ], [
            'username.required' => 'Username tidak boleh kosong.',
            'username.string' => 'Username harus berupa teks.',
            'username.max' => 'Username maksimal 255 karakter.',
            'email.required' => 'Email tidak boleh kosong.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'email.unique' => 'Email sudah digunakan, silakan gunakan email lain.',
            'tanggal_lahir.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
            'jenis_kelamin.in' => 'Jenis kelamin harus laki-laki atau perempuan.',
            'foto_profil.image' => 'File harus berupa gambar.',
            'foto_profil.mimes' => 'Format gambar harus jpeg, png, atau jpg.',
            'foto_profil.max' => 'Ukuran gambar maksimal 2MB.',
            'hapus_foto.boolean' => 'Format hapus foto tidak valid.',
            'tanggal_lahir.before_or_equal' => 'Umur minimal 17 tahun.',
            'tanggal_lahir.required' => 'Tanggal lahir harus diisi',
            'nomor_hp.required' => 'Nomor HP tidak boleh kosong.',
            'nomor_hp.regex' => 'Format nomor HP tidak valid. Contoh: 08123456789.',
        ]);

        if ($request->boolean('hapus_foto')) {
            $this->deleteOldPhoto($user->foto_profil);
            $user->foto_profil = null;
        } elseif ($request->hasFile('foto_profil')) {
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

        $user->fill([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
            'nomor_hp' => $validated['nomor_hp'],
        ]);

        $user->save();

        return back()->with('success', 'Data berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validateWithBag('passwordUpdate', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'current_password.required' => 'Password saat ini tidak boleh kosong.',
            'current_password.current_password' => 'Password saat ini tidak sesuai.',

            'password.required' => 'Password baru tidak boleh kosong.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal :min karakter.',
        ]);

        Auth::user()->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        return back()->with('success', 'Password berhasil diperbarui!');
    }

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
