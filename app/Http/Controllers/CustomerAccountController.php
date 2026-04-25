<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class CustomerAccountController extends Controller
{
    public function index(Request $request)
    {
        // Ambil data user yang rolenya HANYA 'customer'
        $query = User::where('role', 'customer');

        // Logika Pencarian (Berdasarkan ID atau Username)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest()->paginate(8)->withQueryString();

        return view('admin.customer.index', compact('customers'));
    }

    public function show($id)
    {
        // Cari data customer
        $customer = User::findOrFail($id);

        // Jika foto profil ada, buatkan URL lengkapnya. Jika tidak, kembalikan null/default.
        $fotoUrl = $customer->foto_profil ? asset('storage/' . $customer->foto_profil) : asset('images/default-avatar.png');

        // Kembalikan data dalam bentuk JSON agar mudah ditangkap oleh JavaScript di Modal
        return response()->json([
            'id' => $customer->id,
            'username' => $customer->username,
            'email' => $customer->email,
            'tanggal_lahir' => $customer->tanggal_lahir ? \Carbon\Carbon::parse($customer->tanggal_lahir)->format('d - F - Y') : '-',
            'jenis_kelamin' => $customer->jenis_kelamin ? ucfirst($customer->jenis_kelamin) : '-',
            'foto_url' => $fotoUrl
        ]);
    }
}
