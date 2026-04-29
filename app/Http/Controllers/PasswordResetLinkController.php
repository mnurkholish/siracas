<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    public function create()
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Format email harus benar'
        ]);

        // Mengirim link reset ke email
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with('status', 'Link sudah terkirim ke email anda')
                    : back()->withErrors(['email' => 'Email yang dimasukkan tidak terdaftar!']);
    }
}
