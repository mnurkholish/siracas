<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        $customers = $query->latest()->paginate(8)->withQueryString();

        return view('admin.customer.index', compact('customers'));
    }

    public function show($id)
    {
        // Cari data customer
        $customer = User::findOrFail($id);
        abort_unless($customer->role === 'customer', 404);

        // Jika foto profil ada, buatkan URL lengkapnya. Jika tidak, kembalikan null/default.
        $fotoUrl = $customer->foto_profil ? asset('storage/' . $customer->foto_profil) : asset('images/default-avatar.png');

        // Kembalikan data dalam bentuk JSON agar mudah ditangkap oleh JavaScript di Modal
        return response()->json([
            'id' => $customer->id,
            'username' => $customer->username,
            'email' => $customer->email,
            'tanggal_lahir' => $customer->tanggal_lahir ? \Carbon\Carbon::parse($customer->tanggal_lahir)->format('d - F - Y') : '-',
            'jenis_kelamin' => $customer->jenis_kelamin ? ucfirst($customer->jenis_kelamin) : '-',
            'foto_url' => $fotoUrl,
            'nomor_hp' => $customer->nomor_hp,
            'status' => $customer->is_active ? 'Aktif' : 'Nonaktif',
        ]);
    }

    public function updateStatus(Request $request, User $user)
    {
        abort_unless($user->role === 'customer', 404);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ]);

        $user->fill([
            'is_active' => $validated['status'] === 'aktif',
        ]);

        if (! $user->isDirty()) {
            return $this->noChangesResponse();
        }

        $user->save();

        return redirect()
            ->route('admin.customers.index', $request->query())
            ->with('success', $validated['status'] === 'aktif'
                ? 'Akun customer berhasil diaktifkan.'
                : 'Akun customer berhasil dinonaktifkan.');
    }
}
