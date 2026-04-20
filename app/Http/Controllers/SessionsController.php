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
            'login' => ['required', 'string'], // Bisa username atau email
            'password' => ['required', 'string'],
        ]);

        // Cek apakah input adalah email atau username
        $fieldType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (!Auth::attempt([$fieldType => $request->login, 'password' => $request->password], $request->boolean('remember'))) {
            return back()
                ->withErrors(['password' => 'We were unable to authenticate using the providers credentials'])
                ->withInput();
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'))->with('success', 'Login berhasil');
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logout berhasil');
    }
}
