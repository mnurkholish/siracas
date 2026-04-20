<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Jika belum login
        if (!Auth::check()) {
            return view('welcome');
        }

        $user = Auth::user();

        // 2. Jika Admin
        if ($user->role === 'admin') {
            // Kita bisa sekalian passing data untuk dashboard admin di sini
            return view('admin.dashboard');
        }

        // 3. Jika Customer
        return view('customer.dashboard');
    }
}
