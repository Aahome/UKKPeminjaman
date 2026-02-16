<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Halaman login
     */
    public function showLogin()
    {
        // Menampilkan view halaman login
        return view('auth.login');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        // Validasi input username/email dan password dari form login
        $request->validate([
            'username_or_email' => 'required',
            'password'          => 'required'
        ]);

        // Ambil input username atau email
        $input = $request->input('username_or_email');
        
        // Tentukan apakah input adalah email atau username
        $field = filter_var($input, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        // Buat credentials dengan field yang sesuai
        $credentials = [
            $field              => $input,
            'password'          => $request->input('password')
        ];

        // Mencoba autentikasi user menggunakan data credentials
        if (Auth::attempt($credentials)) {
            // Regenerasi session untuk mencegah session fixation
            $request->session()->regenerate();

            // Redirect ke halaman yang sebelumnya dituju atau ke "/" jika tidak ada
            return redirect()->intended('/dashboard');
        }

        // Jika login gagal, kembali ke halaman sebelumnya dengan error
        return back()
            ->withErrors(['username_or_email' => 'Incorrect username/email or password'])
            ->onlyInput('username_or_email'); // Menyimpan kembali input username_or_email
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        // Menghapus autentikasi user yang sedang login
        Auth::logout();

        // Menghapus dan mengamankan session setelah logout
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect ke halaman utama setelah logout
        return redirect('/');
    }
}
