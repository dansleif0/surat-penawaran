<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Menampilkan form login.
     * Jika user sudah login, redirect ke dashboard.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Memproses login menggunakan username & password.
     */
    public function login(Request $request)
    {
        // 1. Validasi input
        $credentials = $request->validate([
            'username' => ['required', 'string'], // Menggunakan username
            'password' => ['required', 'string'],
        ]);

        // 2. Coba login (Attempt)
        // Auth::attempt secara otomatis akan mengenkripsi password input
        // dan mencocokkannya dengan password hash di database.
        if (Auth::attempt($credentials)) {
            // Jika berhasil:
            $request->session()->regenerate(); // Regenerasi session ID untuk keamanan (fix session fixation)

            return redirect()->intended('/'); // Redirect ke halaman tujuan atau home
        }

        // 3. Jika gagal:
        // Kembali ke halaman login dengan pesan error pada kolom username
        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    /**
     * Memproses logout.
     */
    public function logout(Request $request)
    {
        Auth::logout(); // Logout user

        $request->session()->invalidate(); // Invalidasi session saat ini
        $request->session()->regenerateToken(); // Regenerasi CSRF token

        return redirect('/login'); // Kembali ke halaman login
    }
}