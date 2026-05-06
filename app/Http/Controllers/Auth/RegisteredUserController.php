<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'nomor_hp' => ['required', 'regex:/^08[0-9]{8,11}$/'],
            'password' => ['required', Password::defaults()],
            'jenis_kelamin' => ['required', 'in:laki-laki,perempuan'],
            'tanggal_lahir' => ['date', 'before_or_equal:' . Carbon::now()->subYears(17)->format('Y-m-d'),],
        ], [
            'username.required' => 'Username tidak boleh kosong',
            'username.unique' => 'Username sudah dipakai, gunakan yang lain',
            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password tidak boleh kosong',
            'jenis_kelamin.required' => 'Jenis kelamin tidak boleh kosong',
            'jenis_kelamin.select' => 'Jenis kelamin tidak boleh kosong',
            'tanggal_lahir.date' => 'Tanggal lahir tidak boleh kosong',
            'password.min' => 'Password minimal 8 karakter',
            'tanggal_lahir.before_or_equal' => 'Umur minimal 17 tahun.',
            'nomor_hp.regex' => 'Format nomor HP tidak valid (contoh: 08123456789)',
            'nomor_hp.required' => 'Nomor HP tidak boleh kosong',
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'nomor_hp' => $request->nomor_hp,
            'role' => 'customer', // Otomatis menjadi customer
        ]);

        // return redirect()->route('dashboard');
        return redirect()->route('login')->with('success', 'Akun berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
