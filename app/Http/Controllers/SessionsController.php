<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SessionsController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8'],
        ], [
            'email.required' => 'Email tidak boleh kosong.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password tidak boleh kosong.',
            'password.min' => 'Password minimal 8 karakter',
        ]);

        if (!Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ], $request->boolean('remember'))) {
            return back()
                ->withErrors(['password' => 'Login gagal, harap mengisi ulang!'])
                ->withInput();
        }

        $request->session()->regenerate();

        if (Auth::user()->role === 'customer') {
            Auth::user()->cart()->firstOrCreate([]);
        }

        return redirect()->intended(route('home'));
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
